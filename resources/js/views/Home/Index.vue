<template>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150" loader="bars" />
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="card-body">
                <dataset v-slot="{ ds }" :ds-data="items" :ds-sortby="sortBy(columns)">
                    <dataset-show style="display: none;" :ds-show-entries="12"/>
                    <dataset-item class="row mb-3">
                        <template #default="{ row, rowIndex }">
                            <div class="col-sm-6 col-md-3 mb-4">
                                <router-link type="button" class="btn btn-md" :to="`/product/${row.slug}`">
                                    <div class="card mb-2 p-3" style="height:100% !important;">
                                        <div class="card-body pt-3 pb-2 px-3">
                                            <div class="text-center mb-2">
                                                <img :src="row.product_image" class="img-fluid mb-3"
                                                    style="height: 280px; width: 280px;" alt="Default Profile Image">
                                            </div>
                                            <h3 class="card-title text-truncate mb-3" :title="`Index: ${rowIndex}`">
                                                {{ row.name }}
                                                <p class="card-text fs-6 text-center mt-1">{{ row.description == '' ?  'N/A' : row.description }}</p>
                                            </h3>
                                            <p class="card-text text-start fs-4" style="color:#F57224">₱{{ row.price }} </p>
                                            <p class="card-text text-end">{{ row.updated_at }}</p>
                                        </div>
                                    </div>
                                </router-link>
                            </div>
                        </template>
                        <template #noDataFound>
                            <div class="col-md-12 pt-2">
                                <p class="text-center">No results found</p>
                            </div>
                        </template>
                    </dataset-item>
                    <div class="d-flex flex-md-row flex-column justify-content-between align-items-center">
                        <dataset-info class="mb-2 mb-md-0" />
                        <dataset-pager />
                    </div>
                </dataset>
            </div>
        </div>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';
import { formatDate } from '@/helpers/Formatter/Date.js';
import { Dataset, DatasetItem, DatasetInfo, DatasetPager, DatasetSearch, DatasetShow } from 'vue-dataset'
import { sortBy, onColumnSort } from '@/helpers/Dataset/sort.js';
import { useProductStore } from '@/stores/search.js';
export default {
    name: 'Home',

    emits: ['loadUpdatedShops'],

    setup() {
        return { sortBy, onColumnSort };
    },

    data() {
        return {
            items: [],
            shop: null,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
            isLoading: false,
            fullPage: false,
            columns: [
                {
                    field: 'name'
                }
            ],
            selected: 5,
            product: useProductStore()
        }
    },

    components: {
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow,
        Loading,
        Dataset,
    },

    created() {
        this.getPageDetails();
    },

    methods: {
        async getPageDetails() {
            this.isLoading = true;
            await axios.get(`/api/home`, {
                headers: {
                    Authorization: this.auth_token
                }
            })
                .then((response) => {
                    const { products } = response.data;

                    const product_details = products.map(product => {
                        return {
                            ...product,
                            created_at: formatDate(undefined, product.created_at),
                            updated_at: formatDate(undefined, product.updated_at),
                        }
                    })

                    this.items = product_details;
                    this.isLoading = false;
                }).catch((error) => {
                    this.isLoading = false;
                    console.log(error, 'ERROR');
                });
        },

        formatData(items){
            return items.map(product => {
                return {
                    ...product,
                    created_at: formatDate(undefined, product.created_at),
                    updated_at: formatDate(undefined, product.updated_at),
                }
            })
        },
    },

    watch: {
        'product.items'(items) {
            this.items = this.formatData(items);
        }
    }
}
</script>

<style lang="scss" scoped></style>