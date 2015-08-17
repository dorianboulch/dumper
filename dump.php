<?php
require 'vendor/autoload.php';
require 'config.php';

use Ifsnop\Mysqldump as IMysqldump;

$dump_file = date('Y-m-d_H-i-s') . '_' . $config['mysql']['database'] . '.sql';

try{
  $dump = new IMysqldump\Mysqldump($config['mysql']['database'], $config['mysql']['username'], $config['mysql']['password']);
  $dump->start($config['local_directory'].$dump_file);
}catch(\Exception $e){
  die ('mysqldump-php error: ' . $e->getMessage());
}

$ftp_stream = ftp_connect($config['ftp']['server'], $config['ftp']['port'], 5);
ftp_login($ftp_stream, $config['ftp']['user'], $config['ftp']['password']);
ftp_pasv($ftp_stream, true);
if(ftp_put($ftp_stream, $config['ftp']['remote_directory'].$dump_file, $config['local_directory'].$dump_file, FTP_ASCII)){
  echo 'Database "' . $config['mysql']['database'] . '" successfully dumped and sent to the server "' . $config['ftp']['server'] . '" in the directory "' . $config['ftp']['remote_directory'] . '"';
  unlink($config['local_directory'].$dump_file);
}else{
  echo 'ftp-put error';
}
ftp_close($ftp_stream);

?>