<template>
        <div class="d-flex justify-content-center mb-4">
            <div class="col-3 d-flex align-items-center justify-content-center" style="background-color:rgba(128, 128, 128, 0.24);">
                Categories
            </div>
            <div class="col-9"  style="background-color:rgba(128, 128, 128, 0.24);">
                <swiper :pagination="true" :modules="modules" ref="swiper" :loop="true" :autoplay="{
                    delay: 2500,
                    disableOnInteraction: false,
                }">
                    <swiper-slide v-for="photo in photos">
                        <img :src="photo" />
                    </swiper-slide>
                </swiper>
            </div>
        </div>
</template>
<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { Autoplay, Pagination, Navigation } from 'swiper/modules';

export default {
    setup() {
        return {
            modules: [Autoplay, Pagination, Navigation],
        };
    },
    data() {
        return {
            photos: [defaultProduct, defaultProduct, defaultProduct]
        }
    },
    components: {
        Swiper,
        SwiperSlide,
    },
};
</script>
<style scoped>
.swiper-wrapper {
    display: flex;
    align-items: center;
}

.swiper {
    display: flex;
    width: 100%;
    height: 300px;
}

.swiper-slide img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
}


.swiper-pagination-bullet {
    color: black;
    background-color: black;
    width: 12px;
    height: 12px;
}

.swiper-pagination-bullet {
    background-color: aqua;
}
</style>