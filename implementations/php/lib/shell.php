<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Shell
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;


/**
 * Display a message and exit
 */
if(defined('U_MESSAGE')) {
    u_message(U_MESSAGE);
    echo "\n\n";
    exit;
}

/**
 * Clear a VT-100 terminal
 */
$u_cleared = false;
function u_clr() {
    global $u_cleared;
    $u_cleared = true;
    echo chr(27) . "[H" . chr(27) . "[2J";
}

/**
 * Interactive guide bar
 */
function u_bar($active) {
    $commands = array(
        'q' => 'Quit',
        'a' => 'Authors',
        'm' => 'Modules',
        's' => 'Syntax',
        'r' => 'Report Bug',
        ']' => 'Next',
        '[' => 'Previous',
        'y' => 'Yes',
        'n' => 'No',
        'c' => 'Close Menu',
        'x' => 'Exit'
    );
    $allowed = array();
    $bar = '';
    u("");
    foreach(str_split($active) as $command) {
        if(!isset($commands[$command])) {
            continue;
        } else {
            $allowed[$command] = true;
            $desc = $commands[$command];
        }
        $bar .= "| ".strtoupper($command)." $desc ";
    }
    $bar .= "|";
    echo "\n+" . str_repeat('-', strlen($bar) - 2) . '+ ';
    echo "\n" . $bar;
    echo "\n+" . str_repeat('-', strlen($bar) - 2) . '+ ';
    $resp = '@';
    while(!in_array($resp, array_keys($allowed))) {
        $resp = shell_exec('/bin/bash -c "read -s -e -r -N 1 ' .
            'line >/dev/null 2>&1 && echo \$line"');
        $resp = trim($resp);
    }
    return $resp;
}

/**
 * Display information about the current map
 */
function u_shell() {
    u_clr();
    u_hdr();
}

/**
 * Header
 */
function u_hdr() {
    u("Uncomplicated " . Runtime::$version);
}

/**
 * Messages
 */
function u_message($msg) {
    $vrsn = Runtime::$version;
    if($msg !== 'help' && $msg !== 'guide') {
        u_clr();
    }
    switch($msg) {
        case 'banner':
            u("Uncomplicated Programming Language|");
            u("Version:     Clear:      Exit:      Help:");
            u(" $vrsn        Ctrl+L      Ctrl+C     ?");
            return;

        case 'help':
            u("Uncomplicated Programming Language");
            u("|Version:|  $vrsn");
            u("|Implementation:|  PHP");
            u("");
            u("Authors:");
            u("  Nate Ferrero|  http://nateferrero.com");
            u("|Execute a U file:|  uphp path/to/file.u");
            u("|Interactive U shell:|  uphp");
            u("|This information:|  uphp --help");
            break;

        case 'guide':
            $open = true;
            while($open) {
                u_clr();
                u_message('banner');
                u("|Commands:");
                u(" ?                      show this guide");
                u(" ?a                     language authors");
                u(" ?m                     modules");
                u(" ?s                     language syntax");
                u(" ?r                     report a bug");
                u(" ?x                     exit");
                u("|Quick Syntax:");
                u(" # Comment              line comment");
                u(" x: 5 + 2               assign 7 to x in local map");
                u('| y: "one", z: \'two\'     strings');
                u(" x                      show a representation of x");
                switch(u_bar('csmrax')) {
                    case 'a':
                        u_message('authors');
                        break;
                    case 'm':
                        u_message('modules');
                        break;
                    case 's':
                        u_message('syntax');
                        break;
                    case 'r':
                        u_message('report');
                        break;
                    case 'c':
                        $open = false;
                        break;
                    case 'x':
                        u_exit();
                        break;
                }
            }
            break;

        case 'authors':
            u("Authors:");
            u("|Nate Ferrero|    http://nateferrero.com");
            u_bar('c');
            break;

        case 'report':
            u("Report Bug:");
            u("|Email nateferrero@gmail.com with details");
            u_bar('c');
            break;

        case 'modules':
            u("Modules:");
            u("|Module center coming, with the ability to load and export");
            u("modules instantly securely between accounts and publicly.");
            u_bar('c');
            break;

        case 'syntax':
            u("Uncomplicated Syntax:");
            u("  ");
            u_bar('c');
            break;
    }
    u_shell();
}

/**
 * Pretty print
 */
function u($u) {
    global $u_cleared;
    if(!$u_cleared) {
        echo "\n";
    }
    $u_cleared = false;
    echo "| " . str_replace('|', "\n| ", $u);
}

$in_exit = false;

/**
 * Buffer exiting with Ctrl+C
 */
function signal($signal) {

    global $in_exit;

    switch($signal) {
        case SIGTERM:
        case SIGINT:
        default:
            if($in_exit) {
                u_goodbye();
            }
            $in_exit = true;
            if(!u_exit()) {
                u_shell();
                $in_exit = false;
            }
    }
}

/**
 * Exit immediately
 */
function u_goodbye() {
    u_clr();
    exit;
}

/**
 * Confirm before exiting
 */
function u_exit() {
    u_clr();
    u_hdr();
    u("|Are you sure you want to exit?");
    switch(u_bar('yn')) {
        case 'y':
            return u_goodbye();
        case 'n':
            return false;
    }
}

pcntl_signal(SIGTERM, "NateFerrero\\u\\signal");
pcntl_signal(SIGINT, "NateFerrero\\u\\signal");

$engine = new MapEngine();
u_clr();
u_message('banner');

while(true) {
    $content = '';
    if(!$u_cleared) {
        echo "\n\n";
    }

    /**
     * Read a line from the console
     */
    $line = shell_exec('/bin/bash -c "export HISTFILE=~/.u_history;' .
        'read -e -r -p \'| \' -i \'' . 
        addslashes(addslashes($content)) . '\' line && echo \$line"');
    $tline = trim($line);

    /**
     * Handle special commands
     */
    $u_cleared = false;
    if($tline === '') {
        if($line !== null) {
            $u_cleared = true;
        } else {
            if(!u_exit()) {
                u_shell();
            }
        }
        continue;
    } else if($tline === '?') {
        u_message('guide');
        continue;
    } else if($tline === '?s') {
        u_message('syntax');
        continue;
    } else if($tline === '?a') {
        u_message('authors');
        continue;
    } else if($tline === '?m') {
        u_message('modules');
        continue;
    } else if($tline === '?r') {
        u_message('report');
        continue;
    } else if($tline === '?x') {
        if(!u_exit()) {
            u_shell();
        }
        continue;
    } else if($tline[0] === '?') {
        u_message('invalid');
        continue;
    }

    try {
        if($line[strlen($line) -1 ] !== "\n") {
            if(!u_exit()) {
                u_shell();
                continue;
            }
        }
        $tree = Runtime::parse($line);
        $engine->tree($tree);
        echo "|\n| " . Runtime::repr($engine->result);
    } catch(HandledException $e) {
        echo "|\n| " . $e->getMessage();
    } catch(Exception $e) {
        echo "|\n| FATAL " . $e->getMessage();
    }
}