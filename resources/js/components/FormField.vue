<template>
    <default-field :field="field">
        <template slot="field">
            <div class="google-map w-full" :id="mapName"></div><br>
         
               <input 
                    :id="field.name" type="text"
                    ref="autocomplete" 
                   class="w-full form-control form-input form-input-bordered"
                   :class="errorClasses"
                   :placeholder="field.name"
                   v-model="value"
            />
            <div class="flex flex-wrap w-full">
                <div class="flex w-1/2">
                    <div class="w-1/5 py-3 pl-2">
                        <label class="inline-block text-80 pt-2 leading-tight font-bold" :for="field.latitude">Lat</label>
                    </div>
                    <div class="py-3 w-2/3">
                        <input :id="field.latitude" 
                               type="text"
                               class="w-full form-control form-input form-input-bordered"
                               :class="errorClasses"
                               :placeholder="field.latitude"
                               v-model="field.lat"
                        />
                    </div>
                </div>
                <div class="flex w-1/2">
                    <div class="w-1/5 py-3 pl-2">
                        <label class="inline-block text-80 pt-2 leading-tight font-bold" :for="field.longitude">Lng</label>
                    </div>
                    <div class="py-3 w-4/5">
                        <input :id="field.longitude" 
                                type="text"
                               class="w-full form-control form-input form-input-bordered"
                               :class="errorClasses"
                               :placeholder="field.longitude"
                               v-model="field.lng"
                        />
                    </div>
                </div>
            </div>
            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>

        </template>
    </default-field>
</template>
<style scoped>
    .google-map {
        width: 720px;
        height: 300px;
        margin: 0 auto;
        background: gray;
        border:solid 1px #ccc;
    }
</style>
<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        name: 'google-map',
        mixins: [FormField, HandlesValidationErrors],
        props: ['resourceName', 'resourceId', 'field'],
        data: function () {
            return {
                mapName: this.name + "-map",
            }
        },
        mounted: function () {
            let map;
            const element = document.getElementById(this.mapName);
            let lat = this.field.initial_lat || 38.261842;
            let lng = this.field.initial_lng || -0.6868031;

            if (this.field.lat && this.field.lng){
                lat = this.field.lat;
                lng = this.field.lng;
            }
            if (this.value){
                if (previousMarker) {
                    previousMarker.setMap(null);
                }
            }
            const options = {
                zoom: this.field.zoom || 4,
                center: new google.maps.LatLng(lat, lng)
            };
            map = new google.maps.Map(element, options);
            let previousMarker;
            let address = this;
            previousMarker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lng),
                map: map,
                draggable: this.field.drag_enabled ? true: false
            });


            const updateAddress = (event) => {
                  let geocoder = new google.maps.Geocoder;
                    geocoder.geocode({'location': event.latLng}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                let result = results[0];
                                if (address.field.hasOwnProperty('google_result_type')) {
                                    for (let i = 0; i < results.length; i++) {
                                        if (results[i].types.length > 0 && results[i].types.includes(address.field.google_result_type)) {
                                            result = results[i];
                                            break;
                                        }
                                    }
                                }

                                address.value = result.formatted_address;
                                address.field.lat = event.latLng.lat().toFixed(6);
                                address.field.lng = event.latLng.lng().toFixed(6);
                            } else {
                                window.alert('No results found');
                            }
                        } else {
                            window.alert('Geocoder failed due to: ' + status);
                        }
                    });
            }

            // click 
            google.maps.event.addListener(map, 'click', function(event) {


                previousMarker.setPosition(event.latLng)
                map.panTo(event.latLng)

                updateAddress(event, address)
                
            });


            //drag 

            google.maps.event.addListener(previousMarker, 'dragend', function(event) 
            {

                 previousMarker.setPosition(event.latLng)
                 map.panTo(event.latLng)

                updateAddress(event, address)
            });


            if (this.field.enable_autocomplete) {
                var autocomplete = new google.maps.places.Autocomplete((this.$refs.autocomplete), {types: ['geocode']});
                autocomplete.bindTo("bounds", map);

                autocomplete.addListener("place_changed", () => {

                    previousMarker.setVisible(false);

                    const place = autocomplete.getPlace();

                    if (!place.geometry || !place.geometry.location) {
                        // User entered the name of a Place that was not suggested and
                        // pressed the Enter key, or the Place Details request failed.
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                    }

                    previousMarker.setPosition(place.geometry.location);
                    previousMarker.setVisible(true);

                    address.value = place.name;
                    address.field.lat = place.geometry.location.lat().toFixed(6);
                    address.field.lng = place.geometry.location.lng().toFixed(6);

                });
            }
        },
        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || ''
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {

                formData.append(this.field.attribute, this.value || '');
                formData.append(this.field.latitude, this.field.lat || '');
                formData.append(this.field.longitude, this.field.lng || '');


            },
            getAddressData (data) {

            },
            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                console.log( value)
                this.value = value
            }
        },
    }
</script>