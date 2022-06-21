<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import 'vue2-datepicker/locale/ro';
import moment from 'moment';

export default {
  name: 'Range',
  data() {
    return {
    //   value: [new Date(2019, 9, 8), new Date(2019, 9, 19)],
      value: [],
    };
  },
};
</script>

<template>
  <div class="box">
    <section>
        <date-picker
            v-model="value"
            type="date"
            range
            style= "width: '180px'"
            placeholder="Selectează intervalul">
        </date-picker>
    </section>
  </div>
</template>
