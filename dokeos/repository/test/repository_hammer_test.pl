#!/usr/bin/perl

use strict;
use warnings;

use Time::HiRes qw/sleep time/;
use LWP::UserAgent qw//;

use constant LOGIN_URL => 'http://193.191.154.68/dokeoslcms/index.php';
use constant LOGIN_USERNAME => 'admin';
use constant LOGIN_PASSWORD => 'admin';
use constant LEARNING_OBJECT_IDS_URL => 'http://193.191.154.68/dokeoslcms/repository/test/repository_hammer_test.php';
use constant REPOSITORY_URL => 'http://193.191.154.68/dokeoslcms/repository/objectmanagement/';
use constant LEARNING_OBJECT_URL_FORMAT => 'http://193.191.154.68/dokeoslcms/repository/objectmanagement/view.php?id=%d';
use constant LEARNING_OBJECT_OUTPUT_PATTERN => qr/<div class="learning_object">/;
use constant REPOSITORY_OUTPUT_PATTERN => qr/<table class="data_table">/;
use constant REQUEST_COUNT => 5000;
use constant INTERVAL => 0.5;
use constant REPOSITORY_EVERY => 3;
use constant REPORT_EVERY => 10;

$| = 1;

my $ua = new LWP::UserAgent;

print 'Logging in as "' . LOGIN_USERNAME, '" ...';
my $sid_cookie = &login(LOGIN_URL, LOGIN_USERNAME, LOGIN_PASSWORD);
die 'Login failed' unless ($sid_cookie);
print ' Logged in', $/;

print 'Retrieving learning object IDs ...';
my $ids = &get_ids();
die($ids) unless (ref $ids eq 'ARRAY');
print ' Got ', scalar(@$ids), ' IDs', $/, $/;

my %statistics;

foreach my $source (qw/RE LO/) {
	$statistics{$source} = {
		'count' => 0,
		'failures' => 0,
		'total_request_time' => 0,
		'std_dev_total' => 0,
		'avg_time' => 0
	};
}

my $start_time = time();

for (my $i = 1; $i <= REQUEST_COUNT; $i++) {
	my $single_lo = int rand REPOSITORY_EVERY;
	my ($id, $url);
	if ($single_lo) {
		$id = &get_random_id();
		$url = &build_url($id);
	}
	else {
		$url = REPOSITORY_URL;
	}
	my ($response, $request_time) = &get_url($url, $sid_cookie);
	my ($result, $content) = &request_result($response, $id);
	$request_time *= 1000;
	my $info_object = $statistics{$single_lo ? 'LO' : 'RE'};
	$info_object->{'total_request_time'} += $request_time;
	$info_object->{'count'}++;
	$info_object->{'failures'}++ if $result;
	$info_object->{'avg_time'} = $info_object->{'total_request_time'} / $info_object->{'count'};
	$info_object->{'std_dev_total'} += ($info_object->{'avg_time'} - $request_time) ** 2;
	print $i, '.', "\t", (defined $id ? $id : '<R>'), "\t", ($result < 0 ? 'FAIL' : ($result || 'OK')), "\t", sprintf('%.0f', $request_time), ' ms', $/;
	print STDERR $content, $/ if ($result < 0);
	unless ($i % REPORT_EVERY) {
		my $current_time = time();
		my $total_time = $current_time - $start_time;
		print $/,
			'STATUS:                    ', sprintf('%.02f', $i / REQUEST_COUNT * 100), '% complete', $/,
			'Elapsed time:              ', sprintf('%.03f', $total_time), ' seconds', $/;
		while (my ($source, $info) = each %statistics) {
			my $count = $info->{'count'};
			next unless ($count);
			print $source, ' > Average request time: ', sprintf('%.0f', $info->{'avg_time'}), ' ms', $/,
				         '   > Standard deviation:   ', sprintf('%.0f', sqrt($info->{'std_dev_total'} / $count)), ' ms', $/,
				         '   > Failures:             ', $info->{'failures'}, '/', $count, ' (', sprintf('%.02f', $info->{'failures'} / $count * 100), '%)', $/;
		}
		print $/;
	}
	sleep INTERVAL;
}

sub login {
	my ($url, $login, $password) = @_;
	my $response = $ua->post($url, { 'login' => $login, 'password' => $password });
	return $response->header('Set-Cookie');
}

sub get_ids {
	my $response = &get_url(LEARNING_OBJECT_IDS_URL);
	return $response->status_line unless ($response->is_success());
	return [ split(/\n/, $response->content) ];
}

sub get_random_id {
	return $ids->[int rand @$ids];
}

sub build_url {
	my $id = shift;
	return sprintf(LEARNING_OBJECT_URL_FORMAT, $id);
}

sub get_url {
	my ($url, $cookie) = @_;
	my $start = time() if (wantarray);
	my $response = $ua->get($url, 'Cookie' => $cookie);
	my $end = time() if (wantarray);
	return wantarray ? ($response, $end - $start) : $response;
}

sub request_result {
	my ($response, $id) = @_;
	my ($return_code, $return_content) = (0, undef);
	if ($response->is_success()) {
		$return_content = $response->content;
		my $pattern = (defined $id
			? LEARNING_OBJECT_OUTPUT_PATTERN
			: REPOSITORY_OUTPUT_PATTERN);
		$return_code = -1 unless ($return_content =~ $pattern);
	}
	else {
		$return_code = $response->code;
	}
	return (wantarray ? ($return_code, $return_content) : $return_code);
}