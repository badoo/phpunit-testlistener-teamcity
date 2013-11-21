<?php

namespace PHPUnit\TeamCity;

use PHPUnit_Util_Printer;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_TestSuite;
use PHPUnit_Framework_TestFailure;
use PHPUnit_Framework_ExpectationFailedException;
use PHPUnit_Framework_SelfDescribing;
use Exception;

class TestListener extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener
{
    const MESSAGE_SUITE_STARTED = 'testSuiteStarted';
    const MESSAGE_TEST_STARTED = 'testStarted';
    const MESSAGE_TEST_FAILED = 'testFailed';
    const MESSAGE_TEST_IGNORED = 'testIgnored';
    const MESSAGE_TEST_FINISHED = 'testFinished';
    const MESSAGE_COMPARISON_FAILURE = 'comparisonFailure';
    const MESSAGE_SUITE_FINISHED = 'testSuiteFinished';

    /**
     * @param $type
     * @param array $array
     * @return string
     */
    protected function getServiceMessage($type, array $array)
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec = floor($usec * 1000);
        $params = array(
            'timestamp' => date("Y-m-d\\TH:i:s.{$msec}O", $sec),
            'flowId' => getmypid(),
        );
        $params += $array;
        $message = "##teamcity[{$type}";
        foreach ($params as $name => $value) {
            $message .= ' ' . $name . '=' . $this->addSlashes($value) . "'";
        }
        $message .= "]" . PHP_EOL;
        return $message;
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @return string
     */
    protected function getTestName(PHPUnit_Framework_Test $test)
    {
        if ($test instanceof PHPUnit_Framework_SelfDescribing) {
            return $test->toString();
        } else {
            return get_class($test);
        }
    }

    /**
     * An error occurred.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_TEST_FAILED,
            array(
                'name' => $this->getTestName($test),
                'message' => $e->getMessage(),
                'details' => $e->getTraceAsString(),
            )
        );
        $this->write($message);
    }

    /**
     * @param $string
     * @return mixed
     */
    protected function addSlashes($string)
    {
        $search = array(
            "|",
            "'",
            "\n",
            "\r",
            "\u0085",
            "\u2028",
            "\u2029",
            "[",
            "]",
        );
        $replace = array(
            "||",
            "|'",
            "|n",
            "|r",
            "|x",
            "|l",
            "|p",
            "|[",
            "|]",
        );
        return str_replace($search, $replace, $string);
    }

    /**
     * A failure occurred.
     *
     *
     * @param  PHPUnit_Framework_Test|\PHPUnit_Framework_TestCase $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $failures = array();
        $testResult = $test->getTestResultObject();
        /** @var $failure PHPUnit_Framework_TestFailure */
        foreach ($testResult->failures() as $failure) {
            $hash = $e->getMessage() . ' ' . $e->getTraceAsString();
            if (isset($failures[$hash])) {
                continue;
            }

            $array = array(
                'type'     => self::MESSAGE_COMPARISON_FAILURE,
                'name'     => $this->getTestName($test),
                'message'  => $e->getMessage(),
                'details'  => $e->getTraceAsString(),
            );

            $thrownException = $failure->thrownException();
            if ($thrownException instanceof PHPUnit_Framework_ExpectationFailedException) {
                $comparisonFailure = $thrownException->getComparisonFailure();
                $array += array(
                    'expected' => $comparisonFailure->getExpectedAsString(),
                    'actual' => $comparisonFailure->getActualAsString(),
                );
            }

            $message = $this->getServiceMessage(self::MESSAGE_TEST_FAILED, $array);
            $this->write($message);
            $failures[$hash] = true;
        }
    }

    /**
     * Incomplete test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_TEST_IGNORED,
            array(
                'name' => $this->getTestName($test),
                'message' => $e->getMessage(),
            )
        );
        $this->write($message);
    }

    /**
     * Skipped test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     *
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_TEST_IGNORED,
            array(
                'name' => $this->getTestName($test),
                'message' => $e->getMessage(),
            )
        );
        $this->write($message);
    }

    /**
     * A test suite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     *
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_SUITE_STARTED,
            array(
                'name' => $suite->getName(),
            )
        );
        $this->write($message);
    }

    /**
     * A test suite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     *
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_SUITE_FINISHED,
            array(
                'name' => $suite->getName(),
            )
        );
        $this->write($message);
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_TEST_STARTED,
            array(
                'name' => $this->getTestName($test),
                'captureStandardOutput' => 'true',
            )
        );
        $this->write($message);
    }

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test     $test
     * @param  float                      $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $message = $this->getServiceMessage(
            self::MESSAGE_TEST_FINISHED,
            array(
                'name' => $this->getTestName($test),
                'duration' => $time,
            )
        );
        $this->write($message);
    }
}
