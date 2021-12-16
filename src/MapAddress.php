<?php

namespace ISaadSalman\MapAddress;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class MapAddress extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'map_address';

    public $latField = 'lat';
    public $lngField = 'lng';

    /**
     * Set the initial location.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return self
     */
    public function initLocation($latitude, $longitude)
    {
        return $this->withMeta([
            'initial_lat' => $latitude,
            'initial_lng' => $longitude,
        ]);
    }

    /**
     * Set the set location.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return self
     */
    public function setLocation($latitude, $longitude)
    {
        return $this->withMeta([
            'lat' => $latitude,
            'lng' => $longitude,
        ]);
    }

    /**
     * Set the zoom of map.
     *
     * @param $zoom
     *
     * @return self
     */
    public function zoom($zoom)
    {
        return $this->withMeta([
            'zoom' => $zoom,
        ]);
    }


    /**
     * Set marker dragibility.
     *
     * @param $dragEnabled
     *
     * @return self
     */
    public function enableDrag($dragEnabled)
    {
        return $this->withMeta([
            'drag_enabled' => $dragEnabled,
        ]);
    }

    /**
     * Set autocomplete.
     *
     * @param $dragEnabled
     *
     * @return self
     */
    public function enableAutocomplete($autoComplateEnable)
    {
        return $this->withMeta([
            'enable_autocomplete' => $autoComplateEnable,
        ]);
    }

    /**
     * Set the latitude field name.
     *
     * @param $latitude
     *
     * @return self
     */
    public function setLatitudeField($latitude)
    {
        $this->latField = $latitude;
        return $this->withMeta([
            'latitude' => $latitude,
        ]);
    }

    /**
     * Set the longitude field name.
     *
     * @param $longitude
     *
     * @return self
     */
    public function setLongitudeField($longitude)
    {

        $this->lngField = $longitude;

        return $this->withMeta([
            'longitude' => $longitude,
        ]);
    }

    /**
     * Set the result type that should be used from google results. See google documentation for more info: https://developers.google.com/maps/documentation/geocoding/intro#Types
     *
     * @param string $type
     *
     * @return self
     */
    public function setGoogleResultType($type)
    {

        return $this->withMeta([
            'google_result_type' => $type,
        ]);
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {

        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = $request[$requestAttribute];
        }

        // Update lat & lng
        if ($request->exists('lat') && $request->exists('lng') ) {
            $model->{$this->latField} = $request->get('lat');
            $model->{$this->lngField} = $request->get('lng');
        }

    }
}
