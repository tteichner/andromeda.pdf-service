<?php
namespace FAA\Objects;
use FAA\Handler\{Pdf};
use Softwarefactories\AndromedaCore\Obj\Request as BaseRequest;

class Request extends BaseRequest
{
    public function __construct(&$map, $method, $uri)
    {
        parent::__construct($map, $method, $uri);

        // try to find matching handler
        if (!empty($this->map)) {
            foreach($this->map as $ep) {
                if ($ep->method == $method) {
                    if (preg_match($ep->regex, $uri)) {
                        $this->method = $method;
                        $this->uri = $uri;

                        if ($ep->handler === 'Pdf') {
                            $this->function = $ep->function;
                            $this->protected = $ep->protected;
                            $this->handler = new Pdf($this);
                        } else {
                            error_log('ERROR Class not found FAA\\Handler\\' . $ep->handler);
                        }
                    }
                }
            }
        }
    }

    /**
     * The host
     *
     * @return string|null
     */
    public function Host(): ?string
    {
        return (preg_match('/^[a-z\-]+$/', $this->Segment(2))) ? $this->Segment(2) : null;
    }
}
