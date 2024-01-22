<template>
    <loading v-model:active="isLoading" :is-full-page="fullPage" color="#3176FF" :height="150" :weight="150"
        loader="bars" />
    
    <div class="row">
        <div class="col-10 mx-auto my-2">
            <div class="col-12">
                <div class="card p-1 mb-3">
                    <div class="d-flex align-items-center">
                        <img :src="shop.shop_image" class="img-fluid rounded" style="height: 100px; width: 100px;" alt="Shop Image">
                        <div>
                            <p>{{ shop.name }}</p>
                            <p>{{ shop.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <dataset v-slot="{ ds }" :ds-data="items" :ds-sortby="sortBy(columns)">
                    <div class="d-flex justify-content-between align-items-center mb-4" :data-page-count="ds.dsPagecount">
                        <div class="mb-2 mb-md-0">
                            <dataset-show />
                        </div>
                        <div class="col-md-3">
                            <dataset-search class="rounded" ds-search-placeholder="Search Product..." />
                        </div>
                    </div>
                    <dataset-item class="row mb-3">
                        <template #default="{ row, rowIndex }">
                            <div class="col-sm-6 col-md-3 mb-4">
                                <div class="card mb-2 p-3" style="height:100% !important;">
                                    <div class="card-body pt-3 pb-2 px-3">
                                        <div class="text-center mb-2">
                                            <img :src="row.product_image" class="img-fluid mb-3"
                                                style="height: 280px; width: 280px;" alt="Default Profile Image">
                                        </div>
                                        <h3 class="card-title text-truncate mb-3" :title="`Index: ${rowIndex}`">
                                            {{ row.name }}
                                            <p class="card-text fs-6 text-center mt-1">{{ row.description ?? 'N/A' }}</p>
                                        </h3>
                                        <p class="card-text text-start fs-4" style="color:#F57224">₱{{ row.price }} </p>
                                        <p class="card-text text-end">{{ row.updated_at }}</p>
                                    </div>
                                </div>
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
export default {
    name: 'Shop Page Details',

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
            selected: 5
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
        Dataset
    },

    created() {
        this.getPageDetails();
    },

    methods: {
        async getPageDetails() {
            this.isLoading = true;
            await axios.get(`/api/shop-details/${this.$route.params.slug}`, {
                headers: {
                    Authorization: this.auth_token
                }
            })
                .then((response) => {
                    const { shop, products } = response.data;

                    const product_details = products.map(product => {
                        return {
                            ...product,
                            created_at: formatDate(undefined, product.created_at),
                            updated_at: formatDate(undefined, product.updated_at),
                        }
                    })

                    this.shop = shop;
                    this.items = product_details;
                    this.isLoading = false;
                }).catch((error) => {
                    this.isLoading = false;
                    console.log(error, 'ERROR');
                });
        },
    }
}
</script>

<style lang="scss" scoped></style>