<template>
    <div class="row">
        <div class="col-10 mx-auto">
            <!-- <p>{{  name }}</p> -->
            <!-- <button @click="sheesh('sheesh')">Change name</button> -->
            <select v-model="order_type" class="btn btn-warning me-2">
                <option v-for="type in order_types" :key="type.value" :value="type.value">  {{ type.name }}</option>
            </select>
            <button @click="handleSort('title')" class="btn btn-primary">Order By Title</button>
            <button @click="handleSort('location')" class="btn btn-primary ms-2">Order By Location</button>
            <button @click="handleSort('salary')" class="btn btn-primary ms-2">Order By Salary</button>
            <JobList :jobs="jobs" :order="order" :order_type="order_type"/>
        </div>
    </div>
</template>

<script lang="ts">
import { Job } from '@/types/Job';
import { term, type, Ordertypes } from '@/types/OrderTerm';
import JobList from '@/components/Test/JobList.vue';
import { defineComponent } from 'vue'
export default defineComponent({
    data(){
        return{
            name:'Link',
            age: 25 as number | string,
            jobs: [
                {
                    title:'Backend Dev',
                    location: 'Pampanga',
                    salary: 2500,
                    id: 2
                },
                {
                    title:'Accenture',
                    location: 'Zamboanga',
                    salary: 2000,
                    id: 2
                },
                {
                    title:'Front End Dev',
                    location: 'Davao',
                    salary: 3000,
                    id: 2
                },
            ] as Job[],
            order: 'title' as term,
            order_type: 'asc' as type,
            isSaving:25 as string,
            order_types: [
                {
                    name:'Ascending',
                    value: 'asc'
                },
                {
                    name:'Descending',
                    value: 'desc'
                }
            ] as Ordertypes[],
        }
    },
    components:{
        JobList
    },
    methods:{
        sheesh(name: string){
            this.name = name;
        },

        handleSort(term : term){
            this.order = term;
        },

        handleSortType(type : type){
            this.order_type = type;
        },
    }
})
</script>

<style lang="scss" scoped></style>