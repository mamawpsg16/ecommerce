<template>
    <FortuneWheel
      ref="wheelEl"
      style="width: 500px; max-width: 100%;"
      :verify="canvasVerify"
      :canvas="canvasOptions"
      :prizes="prizesCanvas"
      @rotateStart="onCanvasRotateStart"
      @rotateEnd="onRotateEnd"
    />
</template>

<script>
import FortuneWheel from 'vue-fortune-wheel'
import 'vue-fortune-wheel/style.css'
    export default {
        components:{
            FortuneWheel
        },
        data(){
            return{
                
                prizeId:0,
                wheelEl: null,
                canvasVerify:false, // Whether the turntable in canvas mode is enabled for verification
                verifyDuration: 2,
                canvasOptions: {
                    btnWidth: 140,
                    borderColor: '#584b43',
                    borderWidth: 6,
                    lineHeight: 30
                },
                prizesCanvas:[
                    {
                        id: 1, //* The unique id of each prize, an integer greater than 0
                        name: 'Blue', // Prize name, display value when type is canvas (this parameter is not needed when type is image)
                        value: 'Blue\'s value', //* Prize value, return value after spinning
                        bgColor: '#45ace9', // Background color (no need for this parameter when type is image)
                        color: '#ffffff', // Font color (this parameter is not required when type is image)
                        probability: 30 //* Probability, up to 4 decimal places (the sum of the probabilities of all prizes
                    },
                    {
                        id: 2,
                        name: 'Red',
                        value: 'Red\'s value',
                        bgColor: '#dd3832',
                        color: '#ffffff',
                        probability: 40
                    },
                    {
                        id: 3,
                        name: 'Yellow',
                        value: 'Yellow\'s value',
                        bgColor: '#fef151',
                        color: '#ffffff',
                        probability: 30
                    }
                ],
                prizesImage:[
                {
                    id: 1, //* The unique id of each prize, an integer greater than 0
                    value: 'Blue\'s value', //* Prize value, return value after spinning
                    weight: 1 // Weight, if useWeight is true, the probability is calculated by weight (weight must be an integer), so probability is invalid
                },
                {
                    id: 2,
                    value: 'Red\'s value',
                    weight: 0
                },
                {
                    id: 3,
                    value: 'Yellow\'s value',
                    weight: 0
                }
                ]
            }
        },

        computed:{
            prizeRes(){
                return prizesCanvas.find(item => item.id === prizeId.value) || prizesCanvas[0]
            }
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
                    testRequest(verified, verifyDuration * 1000).then((verifiedRes) => {
                    if (verifiedRes) {
                        console.log('Verification passed, start to rotate')
                        rotate() // Call the callback, start spinning
                        this.canvasVerify = false // Turn off verification mode
                    } else {
                        alert('Failed verification')
                    }
                    })
                    return
                }
                console.log('onCanvasRotateStart')
            },

            onImageRotateStart () {
                console.log('onImageRotateStart')
            },

            onRotateEnd (prize) {
                alert(prize.value)
            },

            onChangePrize (id) {
                prizeId.value = id
            }
        },  

        mounted(){
            // this.$refs.wheelEl.startRotate() // Can
        }
    }
</script>

<style lang="scss" scoped>

</style>