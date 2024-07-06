<?php
namespace FAA\Objects;
use FAA\Handler\{Pdf};
use FAA\Interfaces\IHandler;

class Request
{
    /**
     * The http request method
     *
     * @var null
     */
    private $method = null;

    /**
     * Raw request path
     *
     * @var null
     */
    private $uri = null;

    /**
     * This endpoint is protected
     *
     * @var bool
     */
    private $protected = null;

    /**
     * The handler instance
     *
     * @var IHandler|null
     */
    private $handler = null;

    /**
     * Name of the handler function
     *
     * @var null
     */
    private $function = null;

    /**
     * The path segements
     *
     * @var array
     */
    private $segments = array();

    /**
     * Map with endpoints
     *
     * @var array
     */
    private $map;

    /**
     * Request constructor.
     *
     * @param $map
     * @param $method
     * @param $uri
     */
    public function __construct(&$map, $method, $uri)
    {
        // buffer the data
        $this->segments = explode('/', $uri);
        $this->map = $map;

        // try to find matching handler
        if ($this->map && is_array($this->map) && !empty($this->map))
        foreach($this->map as $ep) {
            if ($ep->method == $method) {
                if (preg_match($ep->regex, $uri)) {
                    $this->method = $method;
                    $this->uri = $uri;

                    if ($ep->handler === 'Pdf') {
                        $this->function = $ep->function;
                        $this->protected = $ep->protected;
                        if ($ep->handler === 'Pdf') {
                            $this->handler = new Pdf($this);
                        }
                    } else {
                        error_log('ERROR Class not found FAA\\Handler\\' . $ep->handler);
                    }
                }
            }
        }
    }

    /**
     * Getter for protected state
     *
     * @return bool
     */
    public function IsProtected()
    {
        return $this->protected;
    }

    /**
     * The segment by index or, null
     *
     * @param $index
     * @return mixed|null
     */
    public function Segment($index)
    {
        return (isset($this->segments[$index])) ? $this->segments[$index] : null;
    }

    /**
     * The callback method name
     *
     * @return null
     */
    public function CallBack()
    {
        return $this->function;
    }

    /**
     * @return IHandler|null
     */
    public function Handler()
    {
        return $this->handler;
    }

    /**
     * The host
     *
     * @return string|null
     */
    public function Host()
    {
        return (preg_match('/^[a-z\-]+$/', $this->Segment(2))) ? $this->Segment(2) : null;
    }

    /**
     * The asset id
     *
     * @return string|null
     */
    public function Asset()
    {
        return (preg_match('/^[a-z0-9\-]+$/', $this->Segment(4))) ? $this->Segment(4) : null;
    }
}
