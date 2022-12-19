<?php


namespace Tests\Json;


class JsonResponseBuilder
{
    private $data       = [];
    private $error      = [];
    private $metadata   = [];
    private $links      = [];
    private $is_success = true;

    /**
     * @param array $error
     * @return $this
     */
    public function setError(array $error = []): JsonResponseBuilder
    {
        $this->is_success = false;

        $this->error = $error;

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data = []): JsonResponseBuilder
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $metadata
     * @return $this
     */
    public function setMetadata(array $metadata = []): JsonResponseBuilder
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @param array $links
     * @return $this
     */
    public function setLinks(array $links = []): JsonResponseBuilder
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        if ($this->is_success) {
            $response['data'] = $this->data;
            ! $this->metadata ?: $response['meta'] = $this->metadata;
            ! $this->links ?: $response['links'] = $this->links;
        } else {
            $response['errors'] = $this->error;
        }

        return $response;
    }
}
