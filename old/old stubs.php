<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * break up a block of text to fit onto the screen
 * @param type $text
 * @return string
 */
function breakText($text) {
    $linewidth = 120;
    $len = strlen($text);
    if ($len <= $linewidth) {
        return $text;
    }

// check that lines already are of good length
    $ls = 0;
    $nls = strpos($text, "\n", 0);
    while ($nls - $ls < $linewidth) {
        $ls = $nls;
        $nls = strpos($text, "\n", $nls);
    }
    if ($ls > 0) {
        $outText = substr($text, 0, $ls);
// remove all other remaining line breaks
        $text = str_replace("\n", "", substr($text, $ls));
        $ls = 0;
    } else {
        $outText = "";
    }

// break up any remaining lines
    while (($nlss = $ls + $linewidth - $len) > 0) { // has at least linewidth chars left        
        $nls = strrpos($text, " ", $nlss); // last space position
        $outText . substr($text, $ls, $nls) . "\n";
        $ls = $nls;
    }
    return $outText;
}

/**
 * uses http://gilmation.com/articles/junit-with-standard-and-error-output-in-main-methods/
 */
function createJavaTest($classname, $testno, $name, $decription, $runtimeargs, $input, $output, $outputerr = false) {
    $atest = "    /**\n     * " . $name . "\n     * " . str_replace("\n", "\n     * ", $decription) . "\n     */";
    $atest . "    @Test\n    public void test" . $testno . "() {";
    $atest . "        System.out.println(\"test" . $testno . "\");";
    $atest . "        String[] expectedoutput = new String[]{" . $output . "}";
    if ($outputerr) {
        $atest . "        String[] expectedoutputerr = new String[]{" . $outputerr . "}";
    }
    $atest . "        String[] results = AbstractMainTests.executeMain(" . $classname . ", new String[]{" . $runtimeargs . $input . "})";
    $atest . "        assertArrayEquals(expectedoutput, results);";
    if ($outputerr) {
        $atest . "        assertArrayEquals(expectedoutputerr, results);";
    }
    $atest . "}\n\n";
    return $atest;
}
