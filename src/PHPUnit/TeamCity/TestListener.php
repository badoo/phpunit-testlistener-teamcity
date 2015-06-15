<?php

namespace PHPUnit\TeamCity;

class TestListener extends \PHPUnit_Util_Printer implements \PHPUnit_Framework_TestListener
{
    const MESSAGE_SUITE_STARTED = 'testSuiteStarted';
    const MESSAGE_SUITE_FINISHED = 'testSuiteFinished';
    const MESSAGE_TEST_STARTED = 'testStarted';
    const MESSAGE_TEST_FAILED = 'testFailed';
    const MESSAGE_TEST_IGNORED = 'testIgnored';
    const MESSAGE_TEST_FINISHED = 'testFinished';

    const MESSAGE_COMPARISON_FAILURE = 'comparisonFailure';

    /**
     * @var string
     */
    protected $captureStandardOutput = 'true';

    /**
     * Create and write service message to out
     *
     * @param string $type
     * @param \PHPUnit_Framework_Test $test
     * @param array $params
     */
    protected function writeServiceMessage($type, \PHPUnit_Framework_Test $test, array $params = array())
    {
        $message = $this->createServiceMessage($type, $test, $params);
        $this->write($message);
    }

    /**
     * Create service message
     *
     * @param string $type
     * @param \PHPUnit_Framework_Test $test
     * @param array $params
     * @return string
     */
    protected function createServiceMessage($type, \PHPUnit_Framework_Test $test, array $params = array())
    {
        list($usec, $sec) = explode(' ', microtime());
        $msec = floor($usec * 1000);
        $params += array(
            'name' => $this->getTestName($test),
            'timestamp' => date("Y-m-d\\TH:i:s.{$msec}O", $sec),
            'flowId' => $this->getFlowId($test)
        );
        $message = "##teamcity[{$type}";
        foreach ($params as $name => $value) {
            $message .= ' ' . $name . '=\'' . $this->escapeValue($value) . '\'';
        }
        $message .= "]" . PHP_EOL;
        return $message;
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @return string
     */
    protected function getTestName(\PHPUnit_Framework_Test $test)
    {
        if ($test instanceof \PHPUnit_Framework_TestCase) {
            $name = $test->getName();
        } elseif ($test instanceof \PHPUnit_Framework_TestSuite) {
            $name = $test->getName();
        } elseif ($test instanceof \PHPUnit_Framework_SelfDescribing) {
            $name = $test->toString();
        } else {
            $name = get_class($test);
        }
        return $name;
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @return int
     */
    protected function getFlowId(\PHPUnit_Framework_Test $test)
    {
        return getmypid();
    }

    /**
     * @param string $string
     * @return string
     */
    protected function escapeValue($string)
    {
        return strtr(
            $string,
            array(
                "|"  => "||",
                "'"  => "|'",
                "\n" => "|n",
                "\r" => "|r",
                "["  => "|[",
                "]"  => "|]"
            )
        );
    }

    /**
     * An error occurred.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $params = array(
            'message' => trim(\PHPUnit_Framework_TestFailure::exceptionToString($e)),
            'details' => \PHPUnit_Util_Filter::getFilteredStacktrace($e),
        );

        if ($e instanceof \PHPUnit_Framework_ExpectationFailedException) {
            $comparisonFailure = $e->getComparisonFailure();
            if (null !== $comparisonFailure) {
                $params += array(
                    'type' => self::MESSAGE_COMPARISON_FAILURE,
                    'expected' => $comparisonFailure->getExpectedAsString(),
                    'actual' => $comparisonFailure->getActualAsString()
                );
            }
        }

        $this->writeServiceMessage(
            self::MESSAGE_TEST_FAILED,
            $test,
            $params
        );
    }

    /**
     * A failure occurred.
     *
     * @param \PHPUnit_Framework_Test|\PHPUnit_Framework_TestCase $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->addError($test, $e, $time);
    }

    /**
     * Incomplete test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeServiceMessage(
            self::MESSAGE_TEST_IGNORED,
            $test,
            array(
                'message' => $e->getMessage(),
            )
        );
    }

    /**
     * Risky test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     * @since  Method available since Release 4.0.0
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->addIncompleteTest($test, $e, $time);
    }

    /**
     * Skipped test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeServiceMessage(
            self::MESSAGE_TEST_IGNORED,
            $test,
            array(
                'message' => $e->getMessage(),
            )
        );
    }

    /**
     * A test suite started.
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->writeServiceMessage(
            self::MESSAGE_SUITE_STARTED,
            $suite
        );
    }

    /**
     * A test suite ended.
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->writeServiceMessage(
            self::MESSAGE_SUITE_FINISHED,
            $suite
        );
    }

    /**
     * A test started.
     *
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $this->writeServiceMessage(
            self::MESSAGE_TEST_STARTED,
            $test,
            array(
                'captureStandardOutput' => $this->captureStandardOutput,
            )
        );
    }

    /**
     * A test ended.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param float $time seconds
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        $this->writeServiceMessage(
            self::MESSAGE_TEST_FINISHED,
            $test,
            array(
                'duration' => floor($time * 1000),
            )
        );
    }
}
