<template>
      <Modal class="modal-lg text-center" targetModal="participant-roulette-modal"  :backdrop="true" :escKey="false" modaltitle="Roulette">
        <template #body>
            <template v-if="items.length">
                <FortuneWheel
                 ref="wheel_of_fortune"
                 style="width: 700px; max-width: 100%;"
                 :useWeight="true"
                 :verify="canvasVerify"
                 :duration="10000"
                 :canvas="canvasOptions"
                 :prizes="items"
                 @rotateStart="onCanvasRotateStart"
                 @rotateEnd="onRotateEnd"
               />
            </template>
        </template> 
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import FortuneWheel from 'vue-fortune-wheel'
import defaultProduct from '@/../../public/storage/item/default/dog.png';
import { SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'WheelOfFortune',

        props:{
            items:[Array, Object],
            participant_id:[Number],
            event_id:[Number],
        },
        emits:['toggleConfetti'],
        components:{
            FortuneWheel,
            Modal,
        },

        data(){
            return{
                itemId:0,
                canvasVerify:false, // Whether the turntable in canvas mode is enabled for verification
                verifyDuration: 2,
                canvasOptions: {
                    btnWidth: 150,
                    borderColor: '#E6C805',
                    borderWidth: 6,
                    lineHeight: 20,
                    textRadius:230,
                    fontSize: 20,
                    textLength:18,
                    textDirection:'vertical',
                    btnText:"GO"
                },
                prizes:[
                   
                ],
                done:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
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
                this.$emit('toggleConfetti',false);
                this.done = false;
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

            async storeParticipantItem(prize){
                axios.put('/api/raffle/store-participant-item',{participant_id:this.participant_id, item:prize, event_id:this.event_id},{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    console.log(response,'response wheel of fortune');                      
                })
                .catch((error) => {
                    this.isSaving = false;
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                        SwalDefault.close();
                    }
                });
            },


            async onRotateEnd (prize) {
                await this.storeParticipantItem(prize);

                this.$emit('toggleConfetti',true);
               SwalDefault.fire({
                    title: "Congratulations!",
                    text: `You win ${prize.name}.`,
                    imageUrl: prize.image ? prize.item_image : defaultProduct,
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: "Item image",
                    showConfirmButton: true,
                });
              
                const id = document.getElementById('participant-roulette-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                modal.hide();
            },
        },  

        mounted(){
            console.log(this.$refs.wheel_of_fortune);
        },

        watch:{
            items:{
                handler(data){
                    if(data.length){
                        this.prizes = [];
                        if (this.items && this.items.length) {
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