<?php

/**
 * @architecture Nodes Installation  SCRIPTS
 * @version      1.0.1
 * @copyright    Copyright (c) 2017.
 * 
 */


$user = getenv('USERNAME') ? : getenv('USER');

define('DATE_FORMAT','Y-m-d H:i:s');
define('NODES_LOG'  ,'/tmp/'.$user.'_install_nodes.log');
define('START_SEL'  ,'/tmp/'.$user.'_start_selenium.sh');
define('SEL_LOG'    ,'/tmp/'.$user.'_start_selenium.log');
define('SSH_CONN'   ,'sudo ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o GSSAPIAuthentication=no -p2022 ');

// DB Connection & Querys

function get_connection_Db() 
{
    $host   = '192.168.100.55';
    $port   = '5432';
    $dbname = 'DEVDB';
    $user   = 'advisor';
    $pass   = '^jjlll87676';

    $connection = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    return $connection;
}

function execute_query($query, $connection)
{
    $statement = $connection->prepare($query);
    $result    = $statement->execute();
    if(!$result) 
    {
       $error = $statement->errorInfo();
       logger_files(NODES_LOG,$error);
       return false;
    }
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_query() 
{
    return $query =" SELECT DISTINCT server_entity_id AS id ,server_entity_name AS name,server_main_ip AS main_ip,
                     smtp_port,ssh_port,server_entity_id AS entity_id,lower(entity_name) AS slug FROM ips_view_m 
                     WHERE lower(entity_name)= ";
}

function get_servers($query) 
{
    $conn   = get_connection_Db();
    $result = execute_query($query, $conn);
    return $result;
}


// Check server accessibility

function check_server_connection($host,$port=22) 
{
    try 
    {   
        $server  = $host.":".$port;
        $status  = false;
        $waitTOs = 1;
        if ($fp = @fsockopen($host, $port, $errCode, $errStr, $waitTOs)) 
        {
            logger_files(NODES_LOG,"[+] ====> Success connection to ".$server);
            $status = true;
        } 
        else 
        {
            logger_files(NODES_LOG,"[-] ====> Failed  connection to ".$server.PHP_EOL.$errCode."==>".$errStr.PHP_EOL);
        }
        if ($fp)
            fclose($fp);
        return $status;
    }
    catch (Exception $exc) 
    {
        echo $exc->getTraceAsString();
    }
}


// Check nodes situation

function check_selenium_running($host) 
{
    $selenium_running = false;
    $fp = @fsockopen($host,5555);
    if ($fp !== false) 
    {
        $selenium_running = true;
        fclose($fp);
    }
    return $selenium_running;
}

function check_selenium_nodes($servers_list) 
{
    if (sizeof($servers_list) > 0) 
    {
        foreach ($servers_list as $value)
        {
            $server  = $value['name'];
            $main_ip = $value['main_ip'];
            $status  = check_selenium_running($main_ip)==true ? display("Green",$server.' : '.$main_ip.' is up') : display("Red",$server.' : '.$main_ip.' is down');
        }
        return $status;
    }
}

function check_node_isready($main_ip,$server) 
{
    $srv = $server.' - '.$main_ip.';';
    $log = '/tmp/selenium.log';
    $str = 'The node is registered to the hub and ready to use';
    //display("Yellow",SSH_CONN." $main_ip 'grep -r '$str' $log | wc -l' "); 
    $out = system(SSH_CONN." $main_ip 'grep -r '$str' $log | wc -l' ");
    return isset($out) && $out!=0 ? $srv.'OK': $srv.'DOWN';
}

function check_nodes($servers_list) 
{
    $status = array();
    if (sizeof($servers_list) > 0) 
    {
        foreach ($servers_list as $key => $value)
        {
            $server  = $value['name'];
            $main_ip = $value['main_ip'];
            array_push($status,check_node_isready($main_ip,$server));
        }
    }
    return $status; 
}


// Installation Selenium & Firefox

function install_node($ip,$server) 
{
    $update   ='yum -y update;';
    $jdk_java ='yum -y install java-1.8.0-openjdk Xvfb;';
    //$firefox  ='yum --showduplicates list firefox | expand | grep 52. |  awk \'{ printf $2" "}\'| awk \'{split($0,a," "); print "yum -y install firefox-"a[1]\'}|sh;'; 
    $firefox  ='yum -y remove firefox;yum -y install bzip2;wget https://ftp.mozilla.org/pub/firefox/releases/45.0/linux-x86_64/en-US/firefox-45.0.tar.bz2;tar -xjf firefox-45.0.tar.bz2;sudo rm -rf  /opt/firefox;sudo mv firefox /opt/firefox45;sudo mv /usr/bin/firefox /usr/bin/firefoxold;sudo ln -s /opt/firefox45/firefox /usr/bin/firefox;';    

    $mach_id  ='dbus-uuidgen > /var/lib/dbus/machine-id';
    $selenium ='wget http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar;';
    $firewall ='echo "/sbin/iptables -A INPUT -s 148.251.85.114 -p tcp --dport 5555 -j ACCEPT -m comment --comment \'warmup-app\';" >> /home/EMD/ScriptS/rules/SAVE.sh;/usr/bin/php /home/EMD/ScriptS/firewall.php;';
    $run_sel  ='nohup /usr/bin/xvfb-run -s "-screen 0 1024x768x24" java -jar selenium-server-standalone-2.53.1.jar -role node -hub http://148.251.85.114:4411/grid/register/ -maxSession 20 -browser browserName=firefox,platform=LINUX,maxInstances=20 -log /tmp/selenium.log ';
    
    file_put_contents(START_SEL, $run_sel);
    //$run_sel_1='nohup /usr/bin/xvfb-run  java -jar selenium-server-standalone-2.53.1.jar -role node -hub http://148.251.85.114:4444/grid/register/ -maxSession 20 -browser browserName=firefox,platform=LINUX,maxInstances=20 -log /tmp/selenium.log ';
    //$run_sel_2='nohup  /usr/bin/xvfb-run -s "-screen 0 1920x1200x24"  java -jar selenium-server-standalone-2.53.1.jar -role node -hub http://148.251.85.114:4411/grid/register/ -maxSession 20 -browser browserName=firefox,platform=LINUX,maxInstances=20  -timeout 240 -browserTimeout 240 -log /tmp/selenium.log & ';
    
    logger_files(NODES_LOG,display("bld_Cyan" ,":::::: | Start installation in server  || ( $server  $ip ) || ::::::::  "));
    
    system(SSH_CONN." $ip '$update' & ");
    sleep(1);
    
    logger_files(NODES_LOG,display("und_Red"  ,"{+}--> | Start configuration  Firewall"));
    
    system(SSH_CONN." $ip '$firewall' & ");
    sleep(1);
    
    logger_files(NODES_LOG,display("und_Purple","{+}--> | Start installation JAVA & Firefox"));
    
    system(SSH_CONN." $ip '$jdk_java' & ");
    sleep(1);
    
    system(SSH_CONN." $ip '$firefox' & ");
    sleep(1);
    
    logger_files(NODES_LOG,display("und_Yellow","{+}--> | Start installation  Selenium"));
    
    system(SSH_CONN." $ip '$selenium' & ");
    sleep(1);
    
    system(SSH_CONN." $ip '$mach_id' & ");
    sleep(1);
    
    logger_files(NODES_LOG,display("und_Green" ,"{+}--> | Start running  Selenium"));
    
    system(SSH_CONN." $ip '$run_sel' <". START_SEL." >>". SEL_LOG." & \n exit; ");
    sleep(1);
    
    logger_files(NODES_LOG,display("bld_Cyan"  ,":::::: | End installation in server    || ( $server  $ip ) || ::::::::  ".PHP_EOL));
}

function install_servers_nodes($servers_list) 
{
    if (sizeof($servers_list) > 0) 
    {
        $i = 1;
        foreach ($servers_list as $key => $value)
        {
            $server  = $value['name'];
            $main_ip = $value['main_ip'];
            display('bld_Yellow', $i.'/'.sizeof($servers_list));
            check_server_connection($main_ip,2022) ? install_node($main_ip,$server) : display("Red",$server.' : '.$main_ip.' is unreachable');
            $i++;
        }
    }
}

function refresh_nodes($servers_list) 
{
    $restart_sel ='ps aux | grep -E \'java|firefox|Xvfb|php|httpd|perl|python\' |  awk \'{print "kill -9 "$2}\' | sh;service httpd restart; rm -rf /tmp/webdriver* /tmp/unzip* /tmp/*-* /tmp/anonymous* ; nohup  /usr/bin/xvfb-run -s "-screen 0 1920x1200x24"  java -jar selenium-server-standalone-2.53.1.jar -role node -hub http://148.251.85.114:4411/grid/register/ -maxSession 20 -browser browserName=firefox,platform=LINUX,maxInstances=20  -timeout 240 -browserTimeout 240 -log /tmp/selenium.log &';
    file_put_contents(START_SEL,$restart_sel);
    if (sizeof($servers_list) > 0) 
    {
        foreach ($servers_list as $value)
        {
            $server  = $value['name'];
            $main_ip = $value['main_ip'];
            if(check_server_connection($main_ip,2022))
            {
                logger_files(NODES_LOG,display("Cyan",$server.' : '.$main_ip.PHP_EOL.SSH_CONN.$main_ip." < ". START_SEL." >> ". SEL_LOG.' &'));
                system(SSH_CONN.$main_ip." < ". START_SEL." >> ". SEL_LOG.' &');
            }
            else
            {
              logger_files(NODES_LOG,display("Red",$server.' : '.$main_ip.' is time out'));   
            }

        }
    }
}


// Helpers

function reorganize_servers($str)
{
    $i     = 0;
    $rst   = '';
    $list  = explode(',', $str);
    $items = count($list);
    foreach ($list as $value) 
    {
        if (++$i === $items) 
        {
            $rst.="'" . $value . "'";
        }
        else
        {
            $rst.="'" . $value . "'" . ","; 
        }
    }
    return $rst;
}

function get_nodes_status($list)
{
    foreach ($list as $val) 
    {
        $isReady = explode(';',$val)[1];
        if($isReady=='OK')
        {
          display("Green",' |+| '.explode(';',$val)[0].' => The node is registered to the hub and ready to use ');  
        }
        else
        {
          display("Red"  ,' |-| '.explode(';',$val)[0]);
        }    
    }
}


// Loger

function logger_files($path,$message)
{
    $now = date(DATE_FORMAT);
    if (!file_exists($path))
    {
        touch($path);
    }
    return file_put_contents($path, "$now => :  $message ".PHP_EOL, FILE_APPEND);
}

function execution_time($start) 
{
    $time = (microtime(true) - $start);
    printf('==> It took %.5f sec '.PHP_EOL, $time);
    return $time;
}


// Messages & colors shell

function display($color,$message) 
{
    $colors = array
    (
        'Default'    =>"\033[0m ",
        "Black"      => "\e[0;30m",
        "Red"        => "\e[0;31m",
        "Green"      => "\e[0;32m",
        "Yellow"     => "\e[0;33m",
        "Blue"       => "\e[0;34m",
        "Purple"     => "\e[0;35m",
        "Cyan"       => "\e[0;36m",
        "White"      => "\e[0;37m",
        "bld_Black"  => "\e[1;30m",
        "bld_Red"    => "\e[1;31m",
        "bld_Green"  => "\e[1;32m",
        "bld_Yellow" => "\e[1;33m",
        "bld_Blue"   => "\e[1;34m",
        "bld_Purple" => "\e[1;35m",
        "bld_Cyan"   => "\e[1;36m",
        "bldWhite"   => "\e[1;37m",
        "und_Black"  => "\e[4;30m",
        "und_Red"    => "\e[4;31m",
        "und_Green"  => "\e[4;32m",
        "und_Yellow" => "\e[4;33m",
        "und_Blue"   => "\e[4;34m",
        "und_Purple" => "\e[4;35m",
        "und_Cyan"   => "\e[4;36m",
        "und_White"  => "\e[4;37m",
        "bak_Black"  => "\e[40m",
        "bak_Red"    => "\e[41m",
        "bak_Green"  => "\e[42m",
        "bak_Yellow" => "\e[43m",
        "bak_Blue"   => "\e[44m",
        "bak_Purple" => "\e[45m",
        "bak_Cyan"   => "\e[46m",
        "bak_White"  => "\e[47m",
        "txt_Reset"  => "\e[0m",
    );
    
    $color_shell  = isset($colors[$color]) ? $colors[$color] : $colors['Default'];
    $disply_msg   = $color_shell.$message.$colors['Default'] .PHP_EOL;
    echo    $disply_msg;
    return  $disply_msg;
}

#############################################################################################################################################
#                                                                                                                                           #
#                                                                                                                                           #
#                                                                                                                                           #
#                                                                                                                                           #
#############################################################################################################################################

if (isset($argv[1],$argv[2])) //$argv[1]=slug,$argv[2]=servers(svr1,srv2,....),$argv[3]=(main_ip1,main_ip2,....)
{
    // parameters
    $slug    = isset($argv[1]) ? $argv[1] : '' ;
    $srvs    = isset($argv[2]) ? $argv[2] : '' ;
    $cond    = isset($argv[3]) && $argv[3]=='-ip' ? 'server_main_ip' : 'server_entity_name' ;
    
    // get servers from database 
    $entity  = pg_escape_string(strtolower($slug));
    $servers = reorganize_servers($srvs);
    
    $query   = get_query()."'$entity'  AND $cond IN ($servers)  ORDER BY id ";
    $result  = get_servers($query);
    
    //if(!isset($result) || sizeof($result)==0 )display("Yellow","servers not found") ;
    !isset($result) || sizeof($result)==0 ? display("Yellow","servers not found"): display("Yellow","nodes count is : ".sizeof($result));
    
    if((isset($argv[3]) && $argv[3]=='-r') || (isset($argv[4]) && $argv[4]=='-r')) 
    {
        // refresh  nodes servers
        refresh_nodes($result);
    }
    elseif((isset($argv[3]) && $argv[3]=='-c') || (isset($argv[4]) && $argv[4]=='-c')) 
    {
        // check status  nodes servers
        check_selenium_nodes($result);
    }
    elseif((isset($argv[3]) && $argv[3]=='-i') || (isset($argv[4]) && $argv[4]=='-i') || empty($argv[4]))
    {
        // installations
        $start   = microtime(true);
        install_servers_nodes($result);
        logger_files(NODES_LOG ,"--------  It took : ".gmdate("H:i:s", execution_time($start)));
        // check nodes
        sleep(2);
        check_selenium_nodes($result);
        //$nodes   = check_nodes($result);
        //get_nodes_status($nodes);
    }
    else display("Red"  ,"please check your action  "); 
        
}
else
{
    display("Red"   ,"please check params entity name & servers or main ips"); 
    display("Cyan"  ,"php install_nodes.php [entity_name] [srv_name_1,srv_name_2,... OR main_ip_1,main_ip_2,...] [-ip (if use main ips )] [-action]"); 
    
    display("Cyan"  ,"[-action] ==> ".PHP_EOL."-i ( installation )".PHP_EOL."-r ( restart  )".PHP_EOL."-c ( check status )");
    display("Yellow","Example   ==> ".PHP_EOL."php install_nodes.php emd1 s_emd1_13822,s_emd1_13894 -c".PHP_EOL."php install_nodes.php emd4 104.255.68.191,185.221.132.233 -ip -r");

}




#############################################################################################################################################
#############################################################################################################################################                                                                                                                                           #
#                                                                                                                                           #
#                                                                                                                                           #
#############################################################################################################################################                                                                                                                                           #
#############################################################################################################################################

