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

my $repo_count = 0;
my $repo_failures = 0;
my $repo_total_request_time = 0;
my $repo_std_dev_total = 0;
my $repo_avg_time = 0;

my $lo_count = 0;
my $lo_failures = 0;
my $lo_total_request_time = 0;
my $lo_std_dev_total = 0;
my $lo_avg_time = 0;

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
	if ($single_lo) {
		$lo_total_request_time += $request_time;
		$lo_count++;
		$lo_failures++ unless $result;
		$lo_avg_time = $lo_total_request_time / $lo_count;
		$lo_std_dev_total += ($lo_avg_time - $request_time) ** 2;
	}
	else {
		$repo_total_request_time += $request_time;
		$repo_count++;
		$repo_failures++ unless $result;
		$repo_avg_time = $repo_total_request_time / $repo_count;
		$repo_std_dev_total += ($repo_avg_time - $request_time) ** 2;
	}
	print $i, '.', "\t", (defined $id ? $id : 'R'), "\t", ($result < 0 ? 'FAIL' : ($result || 'OK')), "\t", sprintf('%.0f', $request_time), ' ms', $/;
	print STDERR $content, $/ if ($result < 0);
	unless ($i % REPORT_EVERY) {
		my $current_time = time();
		my $total_time = $current_time - $start_time;
		print $/,
			'STATUS:               ', sprintf('%.02f', $i / REQUEST_COUNT * 100), '% complete', $/,
			'Elapsed time:         ', sprintf('%.03f', $total_time), ' seconds', $/,
			'Average request time: R:  ', ($repo_count ? sprintf('%.0f', $repo_avg_time) : '-'), ' ms', $/,
			'                      LO: ', ($lo_count ? sprintf('%.0f', $lo_avg_time) : '-'), ' ms', $/,
			'Standard deviation:   R:  ', ($repo_count ? sprintf('%.0f', sqrt($repo_std_dev_total / $repo_count)) : '-'), ' ms', $/,
			'                      LO: ', ($lo_count ? sprintf('%.0f', sqrt($lo_std_dev_total / $lo_count)) : '-'), ' ms', $/,
			'Failures:             R:  ', $repo_failures, '/', $repo_count, ' (', sprintf('%.02f', $repo_failures / $repo_count * 100), '%)', $/,
			'                      LO: ', $lo_failures, '/', $lo_count, ' (', sprintf('%.02f', $lo_failures / $lo_count * 100), '%)', $/,
			$/;
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
		if (defined $id) {
			if ($return_content !~ /<div class="learning_object">/) {
				$return_code = -1
			}
		}
		elsif ($return_content !~ /<table class="data_table">/) {
			$return_code = -1;
		}
	}
	return (wantarray ? ($return_code, $return_content) : $return_code);
}