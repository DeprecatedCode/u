<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Grammar
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;

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
        'str-3-s'     => "'''",
        'str-3-d'     => '"""',
        'str-1-s'     => "'",
        'str-1-d'     => '"',
        'expr-{'      => '{',
        'expr-}'      => '}',
        'sep'         => ',',
        'break'       => '/\R+/',
        'space'       => '/\s+/',
        'comment-['   => '/' . '*',
        'comment-]'   => '*' . '/',
        'comment'     => '#',
        'colon'       => ':',
        'group-('     => '(',
        'group-)'     => ')',
        'inclusion'   => '&',
        'dot'         => '.',
        'identifier'  => '/[a-zA-Z_][a-zA-Z0-9_]*/',
        'float'       => '/\d+\.\d*/',
        'int'         => '/\d+/',
        'operator'    => explode(' ', '== != >= <= > < + - * / % ^ ! && ||'),
        'char'        => '/./'
    ),

    /**
     * Root context
     */
    'root' => array(
        '&children' => array(
            'map-[', 'str-3-s', 'str-3-d', 'str-1-s', 'str-1-d',
            'expr-{', 'comment', 'group-('
        ),
        '&inline' => array(
            'sep', 'break', 'space', 'colon', 'identifier',
            'float', 'int', 'operator', 'dot'
        )
    ),

    /**
     * Triple single-quote context
     */
    'str-3-s' => array(
        '&exit' => 'str-3-s',
        '&content' => '&literal'
    ),

    /**
     * Triple double-quote context
     */
    'str-3-d' => array(
        '&exit' => 'str-3-d',
        '&content' => '&literal'
    ),

    /**
     * Single-quote context
     */
    'str-1-s' => array(
        '&children' => array('escape'),
        '&exit' => 'str-1-s',
        '&content' => '&literal'
    ),

    /**
     * Double-quote context
     */
    'str-1-d' => array(
        '&children' => array('escape'),
        '&exit' => 'str-1-d',
        '&content' => '&literal'
    ),

    /**
     * Escape context
     */
    'escape' => array(
        '&exit+' => 'char',
        '&content' => '&literal'
    ),

    /**
     * Map
     */
    'map-[' => array(
        '&content' => 'root',
        '&exit' => 'map-]'
    ),

    /**
     * Expression
     */
    'expr-{' => array(
        '&content' => 'root',
        '&exit' => 'expr-}'
    ),

    /**
     * Inline comment
     */
    'comment' => array(
        '&content' => '&literal',
        '&exit' => 'break'
    ),

    /**
     * Group
     */
    'group-(' => array(
        '&content' => 'root',
        '&exit' => 'group-)'
    )
));
