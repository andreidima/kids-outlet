/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('vue2-datepicker', require('./components/DatePicker.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if (document.querySelector('#app1')) {
    const app1 = new Vue({
        el: '#app1'
    });
}

if (document.querySelector('#angajati')) {
    const app = new Vue({
        el: '#angajati',
        data: {
            angajati: ((typeof angajati !== 'undefined') ? angajati : ''),
            angajat_pontatori: ((typeof angajatPontatori !== 'undefined') ? angajatPontatori : ''),
        }
    });
}

if (document.querySelector('#produs')) {
    const app = new Vue({
        el: '#produs',
        data: {
            nrOperatii: ((typeof nrOperatii !== 'undefined') ? nrOperatii : ''),
            operatii: ((typeof operatii !== 'undefined') ? operatii : ''),
            xls: ((typeof xls !== 'undefined') ? xls : ''),
            xls_array: [],

        },
        // created: function () {
        //     this.formatCells()
        // },
        watch: {
            xls: function () {
                this.xls = this.xls.replace(/\n+$/, "");
                // this.xls_array = this.xls.split(/\r|\n)/gm);
                this.xls_array = this.xls.split(/\n|\t/gi);
                // this.xls_array = this.xls_array.split(/\t/gi);
                console.log(arrGroup.length);
            }
        },

        // methods: {
        //     formatCells(group){

        //         this.xls_array = this.xls.split(/\t/gi);
        //         console.log(arrGroup.length);
        //         // for(var i = 0; i<arrGroup.length; i++){
        //         //     document.forms[0].elements[group + "_" + i].value = arrGroup[i];
        //         // }
        //     }
        // }
    });
}
