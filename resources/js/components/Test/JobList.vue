<template>
    <div>
        <p>Order by {{ order }}</p>
        <TransitionGroup name="list" tag="ul">
            <li v-for="job in orderedJobs" :key="job.id">
                <h2>{{ job.title }} in {{ job.location }}</h2>
                <div class="salary">
                    <p>{{ job.salary }} rupees</p>
                </div>
                <div class="description">
                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eos, veniam sint deleniti nisi facere porro
                        earum magnam dolor. Recusandae exercitationem sapiente deleniti enim nesciunt! Deleniti ducimus
                        accusantium consequatur. Error, excepturi?</p>
                </div>
            </li>
        </TransitionGroup>
    </div>
</template>

<script lang="ts">
import { Job } from '@/types/Job';
import { term, type } from '@/types/OrderTerm';
import { defineComponent, PropType } from 'vue';
export default defineComponent({
    props: {
        jobs: {
            type: Array as PropType<Job[]>,
            required: true
        },
        order: {
            type: String as PropType<term>,
            required: true
        },
        order_type: {
            type: String as PropType<type>,
            required: true
        },
    },
    computed: {
        orderedJobs() : Job[] {
            return [...this.jobs].sort((a: Job, b: Job) => {
                console.log("Comparing:", a[this.order], b[this.order]);

                if (this.order_type == 'desc') {
                    return a[this.order] > b[this.order] ? -1 : 1;
                }

                return a[this.order] < b[this.order] ? -1 : 1;
            });
        }
    }

})
</script>

<style scoped>
.list-move{
    transition:  all 1s;
}
</style>