<template>
    <Modal class="modal-lg" targetModal="create-event-modal" modaltitle="Add Event" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="m-3" id="create-event-form">
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Event Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="event.name" placeholder="Event Name" :class="{ inputInvalidClass : checkInputValidity('event','name',['required']) }" required   autocomplete="event_name" />
                        <div  v-if="v$.event.name.$dirty" :class="{ 'text-danger': checkInputValidity('event','name',['required']) }">
                            <p v-if="v$.event.name.required.$invalid">
                                Event Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Event Date Range <span class="text-danger">*</span></label>
                        <VueDatePicker  v-model="event.date" range multi-calendars :class="{ inputInvalidClass : checkInputValidity('event','date',['required']) || invalidDateRange }" format="MM/dd/yyyy" placeholder="Select Date Range"></VueDatePicker>
                        <div  v-if="v$.event.date.$dirty" :class="{ 'text-danger': checkInputValidity('event','date',['required']) || invalidDateRange }">
                            <span v-if="v$.event.date.required.$invalid">
                                Event Date Range is required.
                            </span>
                            <br v-if="v$.event.date.required.$invalid"/>
                            <span v-if="invalidDateRange">
                                Event Date Range is invalid.
                            </span>
                        </div>
                        <div v-if="errors?.date" class="text-danger">
                            {{ errors?.date[0] }}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label>Event Description</label>
                        <textarea class="form-control" placeholder="Event Description" rows="5" v-model="event.description"/>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-md btn-secondary me-1 px-3" @click="resetForm" alt="reset"><i class="fa-solid fa-rotate-left"></i></button>
                    <button type="submit" class="btn btn-primary btn btn-md btn-primary me-1 px-5">Save</button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import Input from '@/components/Form/Input.vue'
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
    export default {
        name:'Event Create',
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        emits: ['loadUpdatedEvents'],
        data(){
            return{
                invalidDateRange:false,
                event:{
                    name:null,
                    description:null,
                    date:[]
                },
                errors:[{
                    name:false,
                }],
                isSaving:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
            }
        },
        validations () {
            return {
                event: {
                    name: { required },
                    date: { required },
                },
            }
        },
        components:{
            Modal,
            Input,
            VueDatePicker
        },

        async created(){
        },
        methods:{

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            resetForm(){
                this.event = {
                    name:null,
                    description:null,
                };
                this.invalidDateRange= false;
                this.v$.$reset();

            },

            mysqlDateFormat(date){
                // Ensure the month and day are formatted with leading zeros if needed
                const formattedMonth = String(date.getMonth() + 1).padStart(2, '0');
                const formattedDay = String(date.getDate()).padStart(2, '0');

                // Format the date as 'YYYY-MM-DD'
                const formattedMysqlDate = `${date.getFullYear()}-${formattedMonth}-${formattedDay}`;
                return formattedMysqlDate;
            },

            async store(){
                this.isSaving = true;

                const data = {
                    ...this.event,
                    start_date:this.mysqlDateFormat(this.event.date[0]),
                    end_date:this.mysqlDateFormat(this.event.date[1])
                }
                
                axios.post('/api/raffle/events',data,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    this.isSaving = false;
                    if(response.data.errors) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                    }else{
                        this.resetForm();
    
                        this.$emit('loadUpdatedEvents');
                        SwalDefault.fire({
                            icon: "success",
                            text: "Event Successfully Saved.",
                            showConfirmButton: false,
                        });
                    }
                })
                .catch((error) => {
                    this.isSaving = false;
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                        SwalDefault.close();
                    }
                });
            },

            async storeConfirmation() {
                this.invalidDateRange = false;
                const isValidRange = (Boolean(this.event.date[0]) && Boolean(this.event.date[1]));  
                console.log(isValidRange,'isValidRange');
                if(!isValidRange){
                    this.invalidDateRange = true;
                }
                if (!await this.v$.$validate() || !isValidRange) {
                    return;
                }
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.store()
                    }
                });
            },
        },

        watch: {
            isSaving(newValue, oldValue) {
                if (newValue) {
                    SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Saving...',
                        text: "Saving changes, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
            }
        },
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>