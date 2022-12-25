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
            // operatii: ((typeof operatii !== 'undefined') ? operatii : ''),
            // operatii: ((typeof operatii !== 'undefined') ? operatii : ''),
            operatii: [],
            xls: ((typeof xls !== 'undefined') ? xls : ''),
            xls_array: [],

            timp_total: 0,
            pret_total: 0,
        },
        // created: function () {
        //     this.formatCells()
        // },

        methods: {
            formatCells() {
                this.operatii = [];

                this.xls = this.xls.replace(/\n+$/, "");
                // this.xls_array = this.xls.split(/\n|\t/gi);

                this.xls_array = this.xls.split(/\n/g); // Se imparte pe randuri

                this.nrOperatii = this.xls_array.length;

                this.xls_array.forEach((rand, rand_index) => {
                    let rand_array = [];

                    rand_array = (rand.split(/\t/gi)); // Se sparge randul in celule
                    this.operatii[rand_index] = [];
                    rand_array.forEach((celula, celula_index) =>{
                        if (celula_index >= 2){
                            celula = celula.replace(",", ".");
                        }
                        this.operatii[rand_index][celula_index] = celula;
                    })
                });
            this.updateTotaluri();
            },
            updateTotaluri() {
                this.timp_total = 0;
                this.pret_total = 0;
                this.operatii.forEach(element => {
                    this.timp_total += Number(element[2]);
                    this.pret_total += Number(element[3]);
                });
                this.timp_total = Math.round(this.timp_total * 10000000) / 10000000
                this.pret_total = Math.round(this.pret_total * 10000000) / 10000000
            }
        }
    });
}
