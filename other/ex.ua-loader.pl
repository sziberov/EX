#!/usr/bin/perl

########################
#                      #
# ex.ua-loader.pl      #
# http://ex.ua/        #
#                      #
# (c) 2009, bymer      #
# e-mail: bymer@hit.ua #
#                      #

use strict;

use LWP::UserAgent;
use Digest::MD5;
use HTTP::Request::Common;
use Encode;

my $version	= 1;
my $date	= '2009-09-20';
my $version_id	= "0.02 [protocol: $version; date: $date]";

my $ua = LWP::UserAgent->new(
	agent		=> "ex.ua-loader/$version.0",
	keep_alive	=> 1
);

#$ua->proxy('http', 'http://proxy.ex.ua:8080/');
#$ua->env_proxy();

print "http://ex.ua/ file uploader $version_id\n\n";

my $update_ok = 0;
my $update_mode = (stat($0))[2];
my $response = $ua->mirror('http://ex.ru/ex.ua-loader.pl', $0);
if ($response)
{
	if ($response->code() == 200)
	{
		chmod($update_mode, $0) if (defined($update_mode));
		print " - script updated, please restart!\n";
		exit(1);
	}
	$update_ok = 1 if ($response->code() == 304);
}
print " - unable to check last version\n\n" if (!$update_ok);

my $argc = scalar(@ARGV);
if ($argc < 4 or $argc > 5)
{
	print "Usage: ex.ua-loader.pl LOGIN PASSWORD OBJECT_ID FILEMASK <CHARSET> <BLOCK_SIZE>\n\n";
	print "  LOGIN - your login\n";
	print "  PASSWORD - password\n";
	print "  OBJECT_ID - object_id\n";
	print "  FILEMASK - files select mask\n";
	print "  <CHARSET> - OS charset (default - cp1251, not required)\n";
	print "  <BLOCK_SIZE> - block size in KB (default - autotune from 1MB, not required)\n\n";
	print "Examples:\n\n";
	print "  ex.ua-loader.pl bymer *** 123 film.avi\n";
	print "    upload film.avi to object with id 123\n\n";
	print "  ex.ua-loader.pl bymer *** 123 *.avi\n";
	print "    upload all avi files from current folder to object with id 123\n\n";

	exit(2);
}

my $login	= $ARGV[0];
my $password	= $ARGV[1];
my $object_id	= int($ARGV[2]);
my $filemask	= $ARGV[3];
my $charset	= ($ARGV[4] ne '' and $ARGV[4] !~ /^\d+$/) ? lc($ARGV[4]) : 'cp1251';
my $block_size	= ($ARGV[4] =~ /^\d+$/ or $ARGV[5] =~ /^\d+$/) ? $& * 1024 : 0;
my $block_auto	= 0;

my @file_list = ();
if (-f $filemask)
{
	push @file_list, $filemask;
}
else
{
	my $mask = $filemask;
	$mask =~ s/ /\ /g;
	foreach my $filename (sort(glob($mask)))
	{
		push @file_list, $filename if (-f $filename);
	}
}

my $file_count = scalar(@file_list);
if (!$file_count)
{
	print "Unable to find filemask ($filemask)\n";
	exit(3);
}

print " login          : $login\n";
print " password       : ***\n";
print " object_id      : $object_id\n";
print " file(s)        : $filemask ($file_count found)\n";
print " charset        : $charset\n";
print " block_size     : $block_size\n" if ($block_size);

my $msg;
my ($code, $valid, $server_version) = post('i_version', [
	version		=> $version
]);

if ($code != 1)
{
	print "Unable to get version info ($msg)\n";
	exit(4);
}
if (!$valid)
{
	print "Not supported client version [client: $version, server: $server_version]\n";
	exit(5);
}
print " server_version : $server_version\n";

my ($code, $uid, $fs_id, $max_size) = post('i_login', [
	login		=> $login,
	password	=> $password
]);
if ($code != 1)
{
	print "Unable to get login info ($msg)\n";
	exit(6);
}
if (!$max_size)
{
	print "You can't upload files from this account.\n";
	exit(7);
}
print " fs_id          : $fs_id\n";
print " max file size  : $max_size\n";
print " user_id        : ",($uid ? $uid : "login incorrect"),"\n";

my ($code, $oid, $access) = post('i_access', [
	login		=> $login,
	password	=> $password,
	object_id	=> $object_id
]);
if ($code != 1)
{
	print "Unable to get object info ($msg)\n";
	exit(8);
}
print " access         : $access\n\n";

if ($access < 4)
{
	print "You have not write access to object\n";
	exit(9);
}

if ($block_size <= 256 * 1024 or $block_size > 16 * 1024 * 1024)
{
	$block_size = 1024 * 1024;
	$block_auto = 1;
}

foreach my $filename (@file_list)
{
	upload($object_id, $filename);
}
exit(0);

sub getSpeed($$)
{
	my $time = shift();
	my $size = shift();

	my $t = time() - $time;
	$t = 1 if ($t <= 0);

	if ($block_auto and $t > 3)
	{
		my $bs = $size * 5 / $t;
		while ($bs > $block_size * 2 and $block_size <= 8 * 1024 * 1024)
		{
			$block_size *= 2;
		}
	}

	return int($size/$t);
}

sub _post($$)
{
	my $url = shift();
	my $content = shift();

	my $response = $ua->request(
		POST		$url,
		Content_Type	=> 'form-data',
		Content		=> $content
	);

	$msg = undef;
	if ($response->is_success())
	{
		my $c = $response->content();
		if ($c =~ /\n/)
		{
			$msg = $';
			return split(',', $`);
		}
		return split(',', $c);
	}

	return undef;
}

sub post($$)
{
	my $action = shift();
	my $content = shift();

	return _post("http://ex.ru/$action", $content);
}

sub post_fs($$)
{
	my $action = shift();
	my $content = shift();

	return _post("http://ex.ru/lfs/$action", $content);
}

sub upload($$)
{
	my $object_id = shift();
	my $filename = shift();

	my $name = $filename;
	if ($charset !~ /^utf-?8$/)
	{
		eval { Encode::from_to($name, $charset, "utf8"); };
	}

	print "Uploading $filename...\n";

	my ($filesize, $filetime) = (stat($filename))[7, 9];
	if (!defined($filesize))
	{
		print " - unable to get file size\n";
		return 0;
	}
	if ($filesize > $max_size)
	{
		print " - file size exceed max upload size\n";
		return 0;
	}

	my ($code, $fid) = post_fs('f_init', [
		name		=> $name,
		time		=> $filetime,
		size		=> $filesize
	]);

	if ($code != 1)
	{
		print " - failed to start upload ($msg)\n";
		return 0;
	}

	print " - fid = $fid...\n";

	my $time = time();
	my ($file, $buff, $size, $md5);

	if (open($file, $filename))
	{
		my $md5_ctx = Digest::MD5->new();
		my $md5_off = 0;

		binmode($file);
		my $off = 0;
		while (my $length = read($file, $buff, $block_size))
		{
			if ($md5_off == $off)
			{
				$md5_ctx->add($buff) if ($md5_ctx);
				$md5_off = $off + $length;
			}
			elsif ($md5_off > $off and $md5_off < $off + $length)
			{
				$md5_ctx->add(substr($buff, $md5_off - $off)) if ($md5_ctx);
				$md5_off = $off + $length;
			}
			else
			{
				$md5_ctx = undef;
				print " - md5 calculation failed\n";
			}

			while(1)
			{
				($code, $size, $md5) = post_fs('f_write', [
					fid	=> $fid,
					offset	=> $off,
					length	=> $length,
					content	=> $buff
				]);
				if ($code == 1)
				{
					$off += $length;
					last;
				}

				print " - upload block failed ($msg), retrying...\n";
				sleep(1);

				while(1)
				{
					($code, $size, $md5) = post_fs('f_stat', [
						fid	=> $fid
					]);
					last if ($code == 1);

					print " - unable to get file info ($msg), retrying...\n";
					sleep(1);
				}

				if ($size == $off + $length)
				{
					$off += $length;
					last;
				}

				if ($size != $off)
				{
					if (seek($file, $size, 0))
					{
						$off = $size;
						last;
					}
				}
			}

			print " - $off bytes uploaded, ".getSpeed($time, $off)." bytes/sec...\n";
		}
		close($file);

		($code, $size, $md5) = post_fs('f_stat', [
			fid	=> $fid
		]);

		if ($code == 1)
		{
			print " - remote file size is $size, md5 is $md5\n";
		}
		else
		{
			print " - failed to get upload information ($msg)\n";
		}

		my $md5local = $md5_ctx ? $md5_ctx->hexdigest() : $md5;

		print " - local  file size is $filesize, md5 is $md5local\n";

		my $upload_id;
		($code, $upload_id, $size, $md5) = post_fs('f_done', [
			fid		=> $fid,
			name		=> $name,
			time		=> $filetime,
			size		=> $filesize,
			md5		=> $md5local,
			login		=> $login,
			password	=> $password,
			object_id	=> $object_id,
		]);
	
		if ($code == 1)
		{
			print " - file uploaded to object $object_id\n";
			return 1;
		}
		else
		{
			print " - upload failed to object $object_id ($msg)\n";
			return 0;
		}
	}
	else
	{
		($code) = post_fs('f_remove', [
			fid		=> $fid
		]);

		print " - upload failed to $object_id (unable to open file)\n";
		return 0;
	}
}
