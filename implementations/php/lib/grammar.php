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
        'str-3-s'     => "'''",
        'str-3-d'     => '"""',
        'str-1-s'     => "'",
        'str-1-d'     => '"',
        'expr-{'      => '{',
        'expr-}'      => '}',
        'sep'         => ',',
        'break'       => '/\R/',
        'space'       => '/\s/',
        'comment-['   => '/' . '*',
        'comment-]'   => '*' . '/',
        'comment'     => array('#', '/' . '/'),
        'colon'       => ':',
        'group-('     => '(',
        'group-)'     => ')',
        'inclusion'   => '&',
        'dot'         => '.',
        'identifier'  => '/[a-zA-Z_][a-zA-Z0-9_]+/',
        'float'       => '/\d+\.\d*/',
        'int'         => '/\d+/',
        'operator'    => explode(' ', '+ - * / % ^ ! && ||'),
        'comparator'  => explode(' ', '== != >= <= > <'),
        'char'        => '/./'
    ),

    /**
     * Root context
     */
    'root' => array(
        '&child' => array(
            'map-[', 'str-3-s', 'str-3-d', 'str-1-s', 'str-1-d',
            'expr-{', 'comment-[', 'comment', 'group-('
        ),
        '&inline' => array(
            'sep', 'break', 'space', 'colon', 'identifier',
            'float', 'int', 'operator', 'comparator', 'dot'
        )
    ),

    /**
     * Triple single-quote context
     */
    'str-3-s' => array(
        '&exit' => 'str-3-s',
        '&content' => '&literal',
        '&size' => '&inner'
    ),

    /**
     * Triple double-quote context
     */
    'str-3-d' => array(
        '&exit' => 'str-3-d',
        '&content' => '&literal',
        '&size' => '&inner'
    ),

    /**
     * Single-quote context
     */
    'str-1-s' => array(
        '&child' => 'escape',
        '&exit' => 'str-1-s',
        '&content' => '&literal',
        '&size' => '&inner'
    ),

    /**
     * Double-quote context
     */
    'str-1-d' => array(
        '&child' => 'escape',
        '&exit' => 'str-1-d',
        '&content' => '&literal',
        '&size' => '&inner'
    ),

    /**
     * Escape context
     */
    'escape' => array(
        '&exit' => 'char',
        '&content' => '&literal',
        '&size' => '&right'
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
     * Block comment
     */
    'comment-[' => array(
        '&content' => '&literal',
        '&size' => '&inner',
        '&exit' => 'comment-]'
    ),

    /**
     * Inline comment
     */
    'comment' => array(
        '&content' => '&literal',
        '&size' => '&right',
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
