<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Model\Date;
use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Model\DocumentInterface;
use Mattbit\Flat\Exception\DecodeException;

class JsonEncoder implements EncoderInterface
{
    public function encode(DocumentInterface $document)
    {
        $flattened = $this->flatten($document);
        $dates = $this->filterDateAttributes($flattened);

        $data = [
            '_doc' => $document,
        ];

        if (!empty($dates)) {
            $data['_meta'] = [
                'dates' => $dates
            ];
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function decode($data)
    {
        $data = json_decode($data, true);

        if (!isset($data['_doc'])) {
            throw new DecodeException("Document decoding failed because of bad/corrupted data.");
        }

        $document = new Document($data['_doc']);

        if (isset($data['_meta']['dates'])) {
            foreach ($data['_meta']['dates'] as $key) {
                $document->set($key, new Date($document->get($key)));
            }
        }

        return $document;
    }

    public function flatten($document, $prepend = '')
    {
        $results = [];

        foreach ($document as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, $this->flatten($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    public function filterDateAttributes($attributes)
    {
        return array_keys(array_filter($attributes, function($value) {
            return $value instanceof \DateTime;
        }));
    }
}
