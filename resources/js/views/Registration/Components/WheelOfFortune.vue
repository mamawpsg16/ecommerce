<template>
      <Modal class="modal-lg text-center" targetModal="participant-roulette-modal" modaltitle="Roulette">
        <template #body>
            <FortuneWheel
             ref="wheel_of_fortune"
             style="width: 500px; max-width: 100%;"
             :useWeight="true"
             :verify="canvasVerify"
             :canvas="canvasOptions"
             :prizes="prizesFromItems"
             @rotateStart="onCanvasRotateStart"
             @rotateEnd="onRotateEnd"
           />
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import FortuneWheel from 'vue-fortune-wheel'
    export default {
        name:'WheelOfFortune',

        props:{
            items:[Array, Object]
        },

        components:{
            FortuneWheel,
            Modal
        },

        data(){
            return{
                itemId:0,
                canvasVerify:false, // Whether the turntable in canvas mode is enabled for verification
                verifyDuration: 2,
                canvasOptions: {
                    btnWidth: 140,
                    borderColor: '#584b43',
                    borderWidth: 6,
                    lineHeight: 30
                },
                prizes:[
                    {
                        id: 1, 
                        name: 'Example', 
                        value: 'Blue\'s value', 
                        bgColor: '#45ace9', 
                        color: '#ffffff',
                        probability: 100 
                    },
                ]
            }
        },

        computed:{
            prizesFromItems() {
                if (this.items && this.items.length) {
                    return this.items;
                } else {
                    return this.prizes; // fallback to the default prizes if items is not set
                }
            },
        },

        methods:{
            testRequest (verified, duration) { // verified: whether to pass the verification, duration: delay time
                return new Promise((resolve) => {
                    setTimeout(() => {
                    resolve(verified)
                    }, duration)
                })
            },

            onCanvasRotateStart (rotate) {
                if (this.canvasVerify) {
                    const verified = true // true: the test passed the verification, false: the test failed the verification
                    this.testRequest(verified, this.verifyDuration * 1000).then((verifiedRes) => {
                    if (verifiedRes) {
                        rotate() // Call the callback, start spinning
                        this.canvasVerify = false // Turn off verification mode
                    } else {
                        alert('Failed verification')
                    }
                    })
                    return
                }
            },


            onRotateEnd (prize) {
                console.log(prize.value)
            },
        },  

        mounted(){
            console.log(this.$refs.wheel_of_fortune);
        },

        watch:{
            items:{
                handler(data){
                    if(data.length){
                        if (this.items && this.items.length) {
                            this.$nextTick();
                            this.prizes  = this.items;
                        }
                    }
                },
                immediate: true,
                deep: true
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>