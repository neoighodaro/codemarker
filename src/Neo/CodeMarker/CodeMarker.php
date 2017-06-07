<?php

namespace Neo\CodeMarker;

class CodeMarker {

    /**
     * List of markers
     *
     * @var array
     */
    protected $markers = [];

    /**
     * Should log slow calls.
     *
     * @var boolean
     */
    protected $log_slow_calls = true;

    /**
     * Maximum execution time before considered a slow call.
     *
     * @var integer
     */
    protected $max_execution_time = 2;

    /**
     * Class instance.
     *
     * @var CodeMarker
     */
    protected static $instance;

    /**
     * Make constructor private to avoid direct instantiation.
     */
    protected function __construct()
    {
        // Stub
    }

    /**
     * Get an instance of the CodeMarker class.
     *
     * @return CodeMarker
     */
    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the maximum time flag before flagging the call as slow.
     *
     * @param integer $max_execution_time
     */
    public function setMaxTimeFlag($max_execution_time)
    {
        $this->max_execution_time = (int) $max_execution_time;
    }

    /**
     * Activate or deactivate slow calls log.
     *
     * @param boolean $log_calls
     */
    public function setLogSlowCalls($log_calls)
    {
        $this->log_slow_calls = (bool) $log_calls;
    }

    /**
     * Add a marker to a location.
     *
     * @param string $name
     */
    public function addMarker($name)
    {
        $name = strtolower($name);
        $this->markers[$name]['start'] = microtime(true);
    }

    /**
     * Stop the marker added.
     *
     * @param  string $name
     */
    public function stopMarker($name)
    {
        $name = strtolower($name);
        $this->markers[$name]['stop'] = microtime(true);
        $this->markers[$name]['seconds'] = $this->calculateTimeInSeconds($this->markers[$name]['start']);
    }

    /**
     * Profile the code.
     *
     * @param  boolean $only_slow_code
     * @param  boolean $display_markers
     * @return array
     */
    public function profileCode($only_slow_code = false, $display_markers = false, $log_to_file = false)
    {
        if ($display_markers) {
            $this->showPoints($only_slow_code);
        }

        $profile = [];

        foreach ($this->markers as $key => $value) {
            if (($slowCall = $value['stop'] > $this->max_execution_time) OR $only_slow_code === false) {
                if ($slowCall && $this->log_slow_calls) {
                    // @TODO Log slow call
                }

                $profile[$key] = $value['seconds'];
            }
        }

        if($log_to_file) {
            $this->writeToLog($log_to_file);
        }

        return $profile;
    }

    /**
     * Show the points by echoing to the browser.
     *
     * @param  boolean $only_slow_code
     */
    public function showPoints($only_slow_code = false)
    {
        echo '<ul>';

        foreach ($this->markers as $key => $value) {
            if ($only_slow_code === false || $value['seconds'] > $this->max_execution_time) {
                echo "<li><strong>{$key}</strong> took <strong>{$value['seconds']}</strong> seconds to execute.</li>";
            }
        }

        echo '</ul>';
    }

    /**
     * Calculate the time in seconds.
     *
     * @param  integer $start
     * @return integer
     */
    protected function calculateTimeInSeconds($start)
    {
        $end = microtime(true);

        $duration = $end - $start;

        $hours    = (int)($duration/60/60);

        $minutes  = (int)($duration/60)-$hours*60;

        $seconds  = (int)$duration-$hours*60*60-$minutes*60;

        return $seconds;
    }

    protected function writeToLog($log_file)
    {
        $current_time = date("Y-m-d h:i:sa");

        // Open the file -- Verify if file exists else Create it 
        $log = fopen($log_file, "a+");

        fwrite($log, "-------------------------------{$current_time}-----------------------------------\n");

        foreach ($this->markers as $key => $value) {
            fwrite($log, "{$key} took {$value['seconds']} seconds to execute\n");
        }

        fclose($log);
    }
}
