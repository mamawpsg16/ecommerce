<template>
    <div class="row">
        <dataset
             v-slot="{ ds }"
             :ds-data="data"
             :ds-sortby="sortBy(columns)"
         >
         <div class="d-flex justify-content-between align-items-center mb-2" :data-page-count="ds.dsPagecount">
             <div class="mb-2 mb-md-0">
             <dataset-show />
             </div>
             <div class="col-md-4">
             <dataset-search ds-search-placeholder="Search..." />
             </div>
         </div>
         <div class="d-flex justify-content">
             <div class="table-responsive col-md-12">
             <table class="table table-bordered table-hover">
                 <thead>
                 <tr>
                     <th v-for="(th, index) in columns" :key="th.field" :class="['sort', th.sort]"  @click="onColumnSort($event, index,columns)">
                     {{ th.name }} 
                     <!-- <i v-if="th.field !='action'" class="fa-solid fa-sort" ></i> -->
                     </th>
                 </tr>
                 </thead>
                 <dataset-item tag="tbody">
                    <template #default="{ row, rowIndex }">
                        <slot name="body" :data="row" :index="rowIndex">
    
                        </slot>
                    </template>
                 <template #noDataFound>
                    <tr>
                        <td
                            :colspan="columns.length"
                            class="text-center"
                        >
                            No results found
                        </td>
                    </tr>
                 </template>
                 </dataset-item>
             </table>
             </div>
         </div>
         <div class="d-flex flex-md-row flex-column justify-content-between align-items-center">
             <DatasetInfo class="py-3 fs-sm" />
             <DatasetPager class="flex-wrap py-3 fs-sm" />
         </div>
         </dataset>
    </div>
</template>

<script>
import { Dataset, DatasetItem, DatasetInfo, DatasetPager, DatasetSearch, DatasetShow } from 'vue-dataset'
import { sortBy, onColumnSort } from '@/helpers/Dataset/sort.js';
    export default {
        props:['columns','data'],
        setup(){
            return { sortBy, onColumnSort };
        },  
        components:{
            Dataset,
            DatasetItem,
            DatasetInfo,
            DatasetPager,
            DatasetSearch,
            DatasetShow
        }
    }
</script>

<style lang="scss" scoped>

</style>