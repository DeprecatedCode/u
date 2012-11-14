<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Grammar
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;
use NateFerrero\u\Runtime;

/**
 * Setup the sparse parser
 */
Runtime::$parser = new Sparse(array(
    /**
     * All u tokens
     */
    '&tokens' => array(
        'escape'      => '\\',
        'map-['       => '[',
        'map-]'       => ']',
        'str-3-s'     => "'''"
        'str-3-d'     => '"""',
        'str-1-s'     => "'",
        'str-1-d'     => '"'
        'expr-{'      => '{',
        'expr-}'      => '}',
        'sep'         => ',',
        'break'       => '/\R/',
        'space'       => '/\s/',
        'comment-['   => '/' . '*',
        'comment-]'   => '*' . '/',
        'comment'     => array('#', '/' . '/'),
        'value'       => ':',
        'group-('     => '(',
        'group-)'     => ')',
        'inclusion'   => '&'
        'identifier'  => '/[a-zA-Z_][a-zA-Z0-9_]+/',
        'float'       => '/\d+\.\d*/',
        'int'         => '/\d+/',
        'operator'    => explode(' ', '+ - * / % ^ ! && ||'),
        'comparator'  => explode(' ', '== != >= <= > <')
    ),

    /**
     * Root context
     */
    'root' => array(
        '&child' => array(
            'map-[', 'str-3-s', 'str-3-d', 'str-1-s', 'str-1-d',
            'expr-{', 'comment-[', 'comment', 'group-(')
        ),
        '&inline' => array(
            'sep', 'break', 'space', 'value', 'identifier',
            'float', 'int', 'operator', 'comparator'
        )
    ),

    /**
     * Triple single-quote context
     */
    'str-3-s' => array(
        '&exit' => 'str-3-s',
        '&content' => 'inner'
    ),

    /**
     * Triple double-quote context
     */
    'str-3-d' => array(
        '&exit' => 'str-3-d'
        '&content' => 'inner'
    ),

    /**
     * Single-quote context
     */
    'str-1-s' => array(
        '&child' => 'escape',
        '&exit' => 'str-1-s',
        '&content' => 'inner'
    ),

    /**
     * Double-quote context
     */
    'str-1-d' => array(
        '&child' => 'escape',
        '&exit' => 'str-1-d',
        '&content' => 'inner'
    ),

    /**
     * Escape context
     */
    'escape' => array(
        '&exit' => 'char',
        '&content' => 'right'
    ),
));
