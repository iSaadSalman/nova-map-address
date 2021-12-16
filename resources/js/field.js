import Index from './components/IndexField';
import Details from './components/DetailField';
import Form from './components/FormField';
import VueGoogleAutocomplete from 'vue-google-autocomplete'

Nova.booting((Vue, router) => {
    Vue.component('index-map_address', Index);
    Vue.component('detail-map_address', Details);
    Vue.component('form-map_address', Form);
})
