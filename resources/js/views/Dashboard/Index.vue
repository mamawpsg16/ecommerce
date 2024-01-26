<template>
  <!-- <Bar id="my-chart-id" :options="chartOptions" :data="chartData" /> -->
  <div class="div" style="display: block; box-sizing: border-box; height: 500px; width:100%;">
    <Pie :data="data" :options="options"  />
  </div>
</template>
  
<script lang="ts">
// import { Bar } from 'vue-chartjs'
// import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

// ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'
import { defineComponent } from 'vue';
ChartJS.register(ArcElement, Tooltip, Legend)

export default defineComponent({
  name: 'BarChart',
  components: { Pie },
  data() {
    return {
      // chartData: {
      //   labels: ['January', 'February', 'March'],
      //   datasets: [{ data: [40, 20, 12], label: '# of Votes' },]
      // },
      // chartOptions: {
      //   responsive: true
      // }
      data:{
        labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],
        datasets: [
          {
            backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16'],
            data: [40, 20, 80, 10]
          }
        ],
      },
      options:{
        responsive: true,
        maintainAspectRatio: false
      }
    }
  }
})
</script>