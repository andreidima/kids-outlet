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

if (document.querySelector('#gestionareFazeAngajati')) {
    const app = new Vue({
        el: '#gestionareFazeAngajati',
        data: {
            produse: ((typeof produse !== 'undefined') ? produse : ''),
            // angajat_pontatori: ((typeof angajatPontatori !== 'undefined') ? angajatPontatori : ''),
            angajatProduseOperatii: ((typeof angajatProduseOperatii !== 'undefined') ? angajatProduseOperatii : ''),

            numarFaza: '',
            produsSelectat: '',
            operatiiProdusSelectat: [],
            operatieSelectata: '',
        },
        created: function () {
            // se adauga numele produselor la operatii pentru afisarea in pagina
            // console.log(this.angajatProduseOperatii.length);
            for (var i = 0; i < this.angajatProduseOperatii.length; i++) {
                for (var j = 0; j < this.produse.length; j++) {
                    // console.log('a');
                    if (this.angajatProduseOperatii[i].produs_id == this.produse[j].id) {
                        this.angajatProduseOperatii[i].produsNume = this.produse[j].nume
                    }
                }
            }
        },
        watch: {
            produsSelectat: function () {
                this.operatiiProdusSelectat = [];

                for (var i = 0; i < this.produse.length; i++) {
                    if (this.produse[i].id == this.produsSelectat) {
                        for (var j = 0; j < this.produse[i].produse_operatii.length; j++) {
                            this.operatiiProdusSelectat.push(this.produse[i].produse_operatii[j]);
                        }
                    }
                }
            },
            numarFaza: function () {
                // if (this.numarFaza !== ''){
                    for (var i = 0; i < this.operatiiProdusSelectat.length; i++) {
                        if (this.operatiiProdusSelectat[i].numar_de_faza == this.numarFaza) {
                            this.operatieSelectata = this.operatiiProdusSelectat[i].id;
                            return;
                        }
                    }
                    this.operatieSelectata = '';
                // }
            }
        },
        methods: {
            adaugaOperatieAngajatului() {
                // Daca operatia este deja adaugata angajatului, se iese din functie
                for (var i = 0; i < this.angajatProduseOperatii.length; i++) {
                    if (this.angajatProduseOperatii[i].id == this.operatieSelectata) {
                        return;
                    }
                }

                // Se adauga operatia la angajat
                for (var i = 0; i < this.produse.length; i++) {
                    for (var j = 0; j < this.produse[i].produse_operatii.length; j++) {
                        if (this.produse[i].produse_operatii[j].id == this.operatieSelectata) {
                            this.angajatProduseOperatii.push(this.produse[i].produse_operatii[j]);
                            this.angajatProduseOperatii[this.angajatProduseOperatii.length - 1].produsNume = this.produse[i].nume;
                            return;
                        }
                    }
                }
            }
        }
    });
}

if (document.querySelector('#produs')) {
    const app = new Vue({
        el: '#produs',
        data: {
            // nrOperatii: ((typeof nrOperatii !== 'undefined') ? nrOperatii : ''),
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

                // this.nrOperatii = this.xls_array.length;

                this.xls_array.forEach((rand, rand_index) => {
                    let rand_array = [];

                    rand_array = (rand.split(/\t/gi)); // Se sparge randul in celule
                    this.operatii[rand_index] = [];
                    rand_array.forEach((celula, celula_index) =>{
                        if (celula_index >= 2){
                            celula = celula.replace(",", ".");
                            if (!celula || isNaN(celula)){
                                celula = 0;
                            }
                        }
                        this.operatii[rand_index][celula_index] = celula;
                    })
                });

                // Se sterg fazele goale, ce nu au nimic la denumire
                this.operatii.forEach((rand, index) => {
                    if (rand[1] === ""){
                        this.operatii.splice(index, 1);
                    }
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


if (document.querySelector('#produsFazeAngajati')) {
    const app = new Vue({
        el: '#produsFazeAngajati',
        data: {
            produse: ((typeof produse !== 'undefined') ? produse : ''),
            produsSelectat: '',

            angajati: ((typeof angajati !== 'undefined') ? angajati : ''),
            angajatiSelectati: [],
            angajatIdDeAdaugat: '',

            numereDeFaza: '',
            iduriAngajati: '',

            mesajEroare: '',
            mesajSucces: '',
            // angajat_pontatori: ((typeof angajatPontatori !== 'undefined') ? angajatPontatori : ''),
            // angajatProduseOperatii: ((typeof angajatProduseOperatii !== 'undefined') ? angajatProduseOperatii : ''),
        },
        // created: function () {
        //     console.log(this.angajatProduseOperatii.length);
        //     for (var i = 0; i < this.angajatProduseOperatii.length; i++) {
        //         for (var j = 0; j < this.produse.length; j++) {
        //             console.log('a');
        //             if (this.angajatProduseOperatii[i].produs_id == this.produse[j].id) {
        //                 this.angajatProduseOperatii[i].produsNume = this.produse[j].nume
        //             }
        //         }
        //     }
        // },
        watch: {
            angajatIdDeAdaugat: function () {
                this.angajatiSelectati = [];

                if (this.angajatIdDeAdaugat !== '') {
                    for (var i = 0; i < this.angajati.length; i++) {
                        if (this.angajati[i].id == this.angajatIdDeAdaugat) {
                            this.angajatiSelectati.push(this.angajati[i]);
                        }
                    }
                }else{
                    this.angajatiSelectati = this.angajati;
                }
            },
        },
        methods: {
            adaugaAngajatiLaFaze() {
                if (!this.produsSelectat || !this.numereDeFaza || !this.iduriAngajati) {
                    this.mesajEroare = "Vă rugăm să completați toate câmpurile"
                    return;
                } else {
                    this.mesajEroare = "";
                }

                numereDeFaza = this.numereDeFaza.split(",");
                iduriAngajati = this.iduriAngajati.split(",");
                console.log(numereDeFaza, iduriAngajati);

                axios
                    .post('/aplicatie-angajati/produs-faze-angajati/axios',
                        {
                            numereDeFaza: numereDeFaza,
                            iduriAngajati: iduriAngajati
                        },
                        {
                            params: {
                                request: 'adaugareMultipla',
                                produsId: this.produsSelectat,
                            }
                        })
                    .then(function (response) {
                        app.mesajSucces = response.data.raspuns;
                        app.produse = response.data.produse;
                        // console.log(response.data.raspuns);
                    });

            },
            stergeAngajat(indexProdus,indexOperatie,indexAngajat) {
                // console.log(indexProdus,indexOperatie,indexAngajat);
                // console.log(this.produse[indexProdus].produse_operatii[indexOperatie].id);
                // console.log(this.produse[indexProdus].produse_operatii[indexOperatie].angajati[indexAngajat].id);

                axios
                    .delete('/aplicatie-angajati/produs-faze-angajati/axios', {
                        params: {
                            request: 'stergere',
                            operatie_id: this.produse[indexProdus].produse_operatii[indexOperatie].id,
                            angajat_id: this.produse[indexProdus].produse_operatii[indexOperatie].angajati[indexAngajat].id,
                        }
                    });
                this.produse[indexProdus].produse_operatii[indexOperatie].angajati.splice(indexAngajat, 1);
            }
        }
    });
}


if (document.querySelector('#setareAvansuri')) {
    const app = new Vue({
        el: '#setareAvansuri',
        data: {
            angajati: angajati,
            angajatiPerProduri: [[]],
            totalAvansuriPerProduri: [],

            mesajSucces: '',
            avansId: '',
        },
        beforeMount: function () {
            prodMaxim = 0;
            angajati.forEach((angajat) => {
                if (prodMaxim < angajat.prod){
                    prodMaxim = angajat.prod;
                }
            });

            // se creeaza intai arrayul gol
            for (i=0; i <= prodMaxim; i++){
                this.angajatiPerProduri[i] = [];
            }

            // Se adauga angajatii in array la produrile fiecaruia
            angajati.forEach((angajat) => {
                this.angajatiPerProduri[angajat.prod].push(angajat);
            });

            // Se calculeaza totalurile
            for (i = 0; i <= prodMaxim; i++) {
                this.calculeazaTotalAvansuriPerProduri(i);
            }
        },
        methods: {
            actualizeazaAvans(avansId, avansSuma) {
                console.log(avansId, avansSuma);

                axios
                    .post('/avansuri/axios-actualizare-suma',
                        {
                            avansId: avansId,
                            avansSuma: avansSuma
                        },
                        {
                            params: {
                                request: 'actualizareSuma',
                            }
                        })
                    .then(function (response) {
                        app.mesajSucces = response.data.raspuns;
                        app.avansId = response.data.avansId;

                        // Se cauta angajatul in array si i se actulizeaza pretul
                        app.angajatiPerProduri.forEach((angajatiPerProd) => {
                            angajatiPerProd.forEach((angajat) => {
                                if (angajat.avansuri[0].id === avansId){
                                    angajat.avansuri[0].suma = Number(avansSuma);
                                    app.calculeazaTotalAvansuriPerProduri(angajat.prod); // Se recalculeaza si totalul, doar pentru produl respectiv
                                }
                            });
                        });

                    });
            },
            calculeazaTotalAvansuriPerProduri(prod) {
                this.totalAvansuriPerProduri[prod] = 0;
                this.angajatiPerProduri[prod].forEach((angajat) => {
                    this.totalAvansuriPerProduri[prod] += angajat.avansuri[0].suma;
                });
            }
        }
    });
}


if (document.querySelector('#salarii')) {
    const app = new Vue({
        el: '#salarii',
        data: {
            angajati: angajati,
            firmeBtrl: {}, // firme care au angajati cu conturi BTRL
            firmeIng: {}, // firme care au angajati cu conturi ING
            firmeFaraBanca: {}, // firme care au angajati fara conturi bancare
            produse: produse,
            angajatiPerProduri: [[]],

            totalRealizatPerProduri: [],
            totalAvansuriPerProduri: [],
            totalCoPerProduri: [],
            totalMedicalePerProduri: [],
            totalSalariuDeBazaPerProduri: [],
            totalRealizatTotalPerProduri: [],
            totalLichidariPerProduri: [],

            salariulMinimPeEconomie: salariulMinimPeEconomie,
            numarDeZileLucratoare: numarDeZileLucratoare,

            arataProduseleDesfasurat: 'nu',

            mesajSucces: '',
            salariuId: '',
            numeCamp: '',

            numarAngajatiBtrlPerFirma: [[]],
            numarAngajatiIngPerFirma: [[]],
        },
        beforeMount: function () {
            prodMaxim = 0;
            angajati.forEach((angajat) => {
                if (prodMaxim < angajat.prod) {
                    prodMaxim = angajat.prod;
                }
                if (angajat.firma) { // se verifica daca angajatul tine de o firma
                    if (angajat.banca_iban) { // se verifica daca angajatul are cont iban
                        if (angajat.banca_iban.indexOf("BTRL") >= 0) { // Daca ibanul are btrl in nume, se adauga la firma respectiva
                            this.firmeBtrl[angajat.firma] ? (this.firmeBtrl[angajat.firma] += 1) : (this.firmeBtrl[angajat.firma] = 1);
                        } else if (angajat.banca_iban.indexOf("ING") >= 0) { // Daca ibanul are btrl in nume, se adauga la firma respectiva
                            this.firmeIng[angajat.firma] ? (this.firmeIng[angajat.firma] += 1) : (this.firmeIng[angajat.firma] = 1);
                        } else {
                            this.firmeFaraBanca[angajat.firma] ? (this.firmeFaraBanca[angajat.firma] += 1) : (this.firmeFaraBanca[angajat.firma] = 1);
                        }
                    }
                    else {
                        this.firmeFaraBanca[angajat.firma] ? (this.firmeFaraBanca[angajat.firma] += 1) : (this.firmeFaraBanca[angajat.firma] = 1);
                    }
                }
                else { // in cazul in care angajatul nu tine de o firma
                    this.firmeFaraBanca['faraFirma'] ? (this.firmeFaraBanca['faraFirma'] += 1) : (this.firmeFaraBanca['faraFirma'] = 1);
                }
            });

            // se creeaza intai arrayul gol
            for (i = 0; i <= prodMaxim; i++) {
                this.angajatiPerProduri[i] = [];
            }

            // Se adauga angajatii in array la produrile fiecaruia
            angajati.forEach((angajat) => {
                this.angajatiPerProduri[angajat.prod].push(angajat);
            });

            this.calculeazaRealizaturilePeProduse();
            this.calculeazaConcediile();
            // Se calculeaza totalurile
            for (i = 0; i <= prodMaxim; i++) {
                this.calculeazaTotaluriPerProduri(i);
            }
        },
        created: function () {
            // console.log(this.firmeBtrl);
            // console.log(this.firmeIng);
            // this.$nextTick(() => this.calculeazaRealizaturilePeProduse());
            // this.calculeazaRealizaturilePeProduse();
            // this.calculeazaConcediile();
            // setTimeout(() => this.calculeazaRealizaturilePeProduse(),500);
        },
        methods: {
            actualizeazaValoare(salariuId, numeCamp, valoare) {
                axios
                    .post('/salarii/axios-actualizare-valoare',
                        {
                            salariuId: salariuId,
                            numeCamp: numeCamp,
                            valoare: valoare
                        },
                        {
                            params: {
                                request: 'actualizareValoare',
                            }
                        })
                    .then(function (response) {
                        app.mesajSucces = response.data.raspuns;
                        app.salariuId = response.data.salariuId;
                        app.numeCamp = response.data.numeCamp;

                        // Se cauta angajatul in array si i se actulizeaza pretul
                        app.angajatiPerProduri.forEach((angajatiPerProd) => {
                            angajatiPerProd.forEach((angajat) => {
                                if (angajat.salarii[0].id === salariuId) {
                                    angajat.salarii[0][numeCamp] = Number(valoare);
                                    app.calculeazaTotaluriPerProduri(angajat.prod); // Se recalculeaza si totalul, doar pentru produl respectiv
                                }
                            });
                        });
                    });
            },
            calculeazaTotaluriPerProduri(prod) {
                this.totalRealizatPerProduri[prod] = 0;
                this.totalAvansuriPerProduri[prod] = 0;
                this.totalCoPerProduri[prod] = 0;
                this.totalMedicalePerProduri[prod] = 0;
                this.totalSalariuDeBazaPerProduri[prod] = 0;
                this.totalRealizatTotalPerProduri[prod] = 0;
                this.totalLichidariPerProduri[prod] = 0;
                this.angajatiPerProduri[prod].forEach((angajat) => {
                    this.totalRealizatPerProduri[prod] += angajat.realizatTotal;
                    this.totalAvansuriPerProduri[prod] += angajat.salarii[0].avans;
                    this.totalCoPerProduri[prod] += angajat.sumaConcediuOdihna;
                    this.totalMedicalePerProduri[prod] += angajat.sumaConcediuMedical;
                    this.totalSalariuDeBazaPerProduri[prod] += angajat.realizatTotal + angajat.sumaConcediuOdihna + angajat.sumaConcediuMedical;
                    this.totalRealizatTotalPerProduri[prod] += angajat.realizatTotal + angajat.sumaConcediuOdihna + angajat.sumaConcediuMedical;
                    this.totalLichidariPerProduri[prod] += parseFloat(angajat.salarii[0].lichidare);
                });
            },
            calculeazaRealizaturilePeProduse: function () {
                // Calcularea sumelor realizate pe fiecare produs in parte si total REALIZAT
                angajati.forEach((angajat) => {
                    realizatProduse = [];
                    realizatTotal = 0;
                    produse.forEach((produs) => {
                        realizat = 0;
                        produs.produse_operatii.forEach((operatie) => {
                            // foreach ($angajat->norme_lucrate->where('produs_operatie_id', $produs_operatie->id) as $norma_lucrata){
                            angajat.norme_lucrate.forEach((norma) => {
                                if (norma.produs_operatie_id === operatie.id) {
                                    realizat += norma.cantitate * operatie.pret;
                                }
                            });
                        });
                        realizatProduse[produs.id] = realizat;
                        realizatTotal += realizat;
                    });
                    angajat.realizatProduse = realizatProduse; // Se adauga la angajat arrayul cu realizatul per produs
                    angajat.realizatTotal = realizatTotal; // Se adauga la angajat realizatTotal
                });
            },
            calculeazaConcediile: function () {
                // Calcularea concediului medical si a celui de odihna
                angajati.forEach((angajat) => {
                    zile_concediu_medical = 0;
                    zile_concediu_de_odihna = 0;
                    angajat.pontaj.forEach((pontaj) => {
                        if (pontaj.concediu == 1) {
                            zile_concediu_medical++;
                        } else if (pontaj.concediu == 2) {
                            zile_concediu_de_odihna++;
                        }
                    });
                    angajat.sumaConcediuOdihna = this.salariulMinimPeEconomie / this.numarDeZileLucratoare * zile_concediu_de_odihna;
                    angajat.sumaConcediuMedical = this.salariulMinimPeEconomie / this.numarDeZileLucratoare * zile_concediu_medical * 0.75;
                });
            },
        }
    });
}
