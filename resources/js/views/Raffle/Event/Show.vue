<template>
    <Modal class="modal-lg" targetModal="event-details-modal" :modaltitle="event.name" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="m-3" id="event-details-form">
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Event Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="event.name" :disabled="!edit" placeholder="Event Name" :class="{ inputInvalidClass : checkInputValidity('event','name',['required']) }" required   autocomplete="event_name" />
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
                        <VueDatePicker v-if="edit" v-model="event.date" range multi-calendars :class="{ inputInvalidClass : checkInputValidity('event','date',['required']) || invalidDateRange }" format="MM/dd/yyyy" placeholder="Select Date Range"></VueDatePicker>
                        <Input type="text" v-else :disabled="!edit" v-model="event.date_range" placeholder="Event Name" :class="{ inputInvalidClass : checkInputValidity('event','name',['required']) }" required   autocomplete="event_name" />
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
                        <textarea class="form-control" :disabled="!edit"  rows="5" v-model="event.description"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Status</label>
                        <div class="form-check form-switch d-flex align-items-center" style="margin:0">
                            <input class="form-check-input" type="checkbox" :disabled="!edit" value="" id="example-switch-default1" name="example-switch-default1" :checked="event.active" @change="handleStatusToggle"/>
                            <label class="form-check-label" for="example-switch-default1">
                                <span :class="`btn btn-sm ms-2 px-4 btn-${ event.active ? 'success': 'danger'} btn-xs rounded-pill`">{{ event.active ? "Active" : "Inactive" }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" v-if="!edit" class="btn btn-md btn-secondary me-1 px-3" @click="closeModal">Close</button>
                    <button type="button"  v-if="!edit" class="btn btn-md btn-primary me-1 px-3" @click="editDetails"><i class="fa-solid fa-pencil"></i></button>
                    <div v-else>
                        <button type="button" class="btn btn-md btn-danger me-1 px-3" @click="cancelEdit">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary me-1 px-3">Update</button>
                    </div>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import Input from '@/components/Form/Input.vue'
import VueMultiselect from 'vue-multiselect'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { deepClone } from '@/helpers/PartialHelpers/index.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { formatDate, formatDateSlash } from '@/helpers/Formatter/Date.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'Event Details',
        props:{
            event_details: [Object, Array],
            event_id: [Number],
            index: [Number],
            date:[Array]
        },
        emits: ['updateEvent'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                invalidDateRange:false,
                event:{
                    name:null,
                    description:null,
                    date:[],
                },
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                prevDetails:null,
                errors:[{
                    name:false,
                    date:false
                }],
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
            VueDatePicker,
            VueMultiselect,
            VueDatePicker
        },

        async created(){
        },

        methods:{
            resetForm() {
                this.invalidDateRange= false;
                this.event = deepClone(this.prevDetails);
                this.event.date = [new Date(this.prevDetails.start_date), new Date(this.prevDetails.end_date)];
                this.errors = [];
                this.v$.$reset();
            },

            editDetails(){
                this.edit = true;
                this.resetForm();
            },

            cancelEdit(){
                this.edit = false;
                this.resetForm();
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            formatData(data){
                const start_date = formatDate(undefined, data.start_date, 'date');
                const end_date = formatDate(undefined, data.end_date, 'date');
                const details = {                            
                        ...data,
                        status:data.active ? 'Active' : 'Inactive',
                        created_at: formatDateSlash(data.created_at),
                        updated_at: formatDateSlash(data.updated_at),
                        date_range: `${start_date} - ${end_date}`,
                        date: [new Date(data.start_date), new Date( data.end_date)]
                }
                return details;
            },

            closeModal(){
                const id = document.getElementById('event-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.resetForm();
                modal.hide();
            },

            mysqlDateFormat(date){
                // Ensure the month and day are formatted with leading zeros if needed
                const formattedMonth = String(date.getMonth() + 1).padStart(2, '0');
                const formattedDay = String(date.getDate()).padStart(2, '0');

                // Format the date as 'YYYY-MM-DD'
                const formattedMysqlDate = `${date.getFullYear()}-${formattedMonth}-${formattedDay}`;
                return formattedMysqlDate;
            },

            async update(){
                this.isUpdating = true;

                 const data = {
                    ...this.event,
                    start_date:this.mysqlDateFormat(this.event.date[0]),
                    end_date:this.mysqlDateFormat(this.event.date[1])
                }
                axios.put(`/api/raffle/events/${this.event_id}`,data,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data } = response.data;
                    this.isUpdating = false;
                    console.log(response.data, response.data.errors, data, 'response, data');
                    if(response.data?.errors) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                        return;
                    }else{
                        this.edit = false;
                        const formattedData = this.formatData(data);
                        this.prevDetails = deepClone(formattedData);
                        this.$emit('updateEvent', this.index, formattedData);
                        SwalDefault.fire({
                            icon: "success",
                            text: "Succesfully updated!",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        this.resetForm();
                        this.errors = []; 
                    }
                    
                  
                })
                .catch((error) => {
                    this.isUpdating = false;
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                        SwalDefault.close();
                    }
                });
            },


            async updateConfirmation(){
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
                       this.update()
                    }
                });
            },

            handleStatusToggle() {
                this.event.active = !this.event.active;
            },
        },

        mounted(){
        },

        watch:{
            event_details(){
                this.edit = false;
                const data  = this.event_details;
                data.date = this.date;
                this.prevDetails = deepClone(data);
                this.resetForm();
                this.event = this.event_details;
                immediate:true
            },

            isUpdating(newValue, oldValue) {
                if (newValue) {
                    SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Updating...',
                        text: "Saving changes, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
            }
        }
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>