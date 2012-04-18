<?php
$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
    ->setName('PHPUnit_TestListener_TeamCity')
    ->setChannel('badoo.github.com')
    ->setSummary('PHPUnit & TeamCity Integration')
    ->setDescription("Based on PHPUnit's TestListner and TeamCity's Service Messages providing realtime test reporting during build process.")
    ->setReleaseVersion('0.1.1')
    ->setReleaseStability('beta')
    ->setApiVersion('0.1.0')
    ->setApiStability('beta')
    ->setLicense(Pearfarm_PackageSpec::LICENSE_MIT)
    ->setNotes('Updates after internal testing')
    ->addMaintainer('lead', 'Alexander Ilyin', 'alexanderilyin', 'alexande@ilyin.eu')
    ->addGitFiles();