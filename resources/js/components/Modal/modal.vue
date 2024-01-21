<template>
    <!-- Modal -->
    <Teleport to="#modals">
        <div class="modal fade" :id="`${targetModal}`" tabindex="-1" :data-bs-keyboard="escKey" :data-bs-backdrop="backdrop ? 'static' : null" :aria-labelledby="`${targetModal}Label`" aria-hidden="true">
            <div v-bind="$attrs" class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" :id="`${targetModal}Label`" v-if="modaltitle">{{ modaltitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <slot name="body">
                            
                        </slot>
                    </div>
                    <div class="modal-footer" v-if="footer">
                        <slot name="footer">
    
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>

</template>

<script>
    export default {
        props:{
           targetModal:{
            type:String
           },
           modaltitle:{
            default:null
           },
           backdrop:{
            type:Boolean,
            default:false
           },
           escKey:{
            type:Boolean,
            default:true
           },
           footer:{
            type:Boolean,
            default:false
           }
        },
    }
</script>

<style lang="scss" scoped>

</style>