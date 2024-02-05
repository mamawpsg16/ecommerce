<template>
    <Modal class="modal-lg" targetModal="item-details-modal" :modaltitle="item.name" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="m-1" id="item-details-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultItemImage" class="img-fluid mb-4 rounded"
                            style="height: 280px; width: 280px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <template v-if="edit">
                            <div class="d-flex justify-content-center align-items-center">
                                <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 280px;"
                                    @change="uploadImage" accept="image/*">
                                <button v-if="image" type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i
                                        class="fa-solid fa-trash"></i></button>
                            </div>
                            <div v-if="errors?.image" class="text-danger">
                                {{ errors?.image[0] }}
                            </div>
                        </template>
                    </div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-6">
                        <label class="mb-1">Event <span class="text-danger" v-if="edit">*</span></label>
                        <VueMultiselect v-if="edit" :loading="loadingEvents" :disabled="loadingEvents" :class="{ inputInvalidClass : checkInputValidity('item','event',['required'])}" v-model="item.event" track-by="label" label="label" placeholder="Event" :options="events"></VueMultiselect>
                        <Input type="text" v-else :disabled="!edit" v-model="item.event_name" :class="{ inputInvalidClass: checkInputValidity('item', 'event', ['required']) }" required autocomplete="item_event" />
                        <div  v-if="v$.item.event.$dirty" :class="{ 'text-danger': checkInputValidity('item','event',['required'])}">
                            <span v-if="v$.item.event.required.$invalid">
                                Event is required.
                            </span>
                        </div>
                    </div>
                </div>
                <!-- <div class="row justify-content-end mb-3">
                    <div class="col-4">
                        <label>Order <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text" :disabled="!edit" v-model="item.order" :class="{ inputInvalidClass: checkInputValidity('item', 'order', ['required']) }" required autocomplete="item_order" />
                        <div v-if="v$.item.order.$dirty"
                            :class="{ 'text-danger': checkInputValidity('item', 'order', ['required']) }">
                            <p v-if="v$.item.order.required.$invalid">
                                Order is required.
                            </p>
                        </div>
                        <div v-if="errors?.order" class="text-danger">
                            {{ errors?.order[0] }}
                        </div>
                    </div>
                </div> -->

                <div class="row mb-3">
                    <div class="col-4">
                        <label>Name <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text" :disabled="!edit" v-model="item.name" :class="{ inputInvalidClass: checkInputValidity('item', 'name', ['required']) }" required autocomplete="item_name" />
                        <div v-if="v$.item.name.$dirty"
                            :class="{ 'text-danger': checkInputValidity('item', 'name', ['required']) }">
                            <p v-if="v$.item.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Quantity <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="number" :disabled="!edit"  v-model="item.quantity" :class="{ inputInvalidClass: checkInputValidity('item', 'quantity', ['required']) }" required
                            autocomplete="quantity" />
                        <div v-if="v$.item.quantity.$dirty" :class="{ 'text-danger': checkInputValidity('item', 'quantity', ['required']) }">
                            <p v-if="v$.item.quantity.required.$invalid">
                                Quantity is required.
                            </p>
                        </div>
                        <div v-if="errors?.quantity" class="text-danger">
                            {{ errors?.quantity[0] }}
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Chance Rate<span class="text-danger" v-if="edit">*</span></label>
                        <Input type="number" :disabled="!edit" v-model="item.chance_rate" :class="{ inputInvalidClass: checkInputValidity('item', 'chance_rate', ['required']) }" required
                            autocomplete="chance_rate" />
                        <div v-if="v$.item.chance_rate.$dirty" :class="{ 'text-danger': checkInputValidity('item', 'chance_rate', ['required']) }">
                            <p v-if="v$.item.chance_rate.required.$invalid">
                                Chance Rate is required.
                            </p>
                        </div>
                        <div v-if="errors?.chance_rate" class="text-danger">
                            {{ errors?.chance_rate[0] }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4">
                        <label>Color <code>(Hex)</code><span class="text-danger">*</span></label>
                        <Input type="text" v-model="item.color" :disabled="!edit" :class="{ inputInvalidClass: checkInputValidity('item', 'color', ['required']) }"  autocomplete="item_color" />
                        <div v-if="v$.item.color.$dirty"
                            :class="{ 'text-danger': checkInputValidity('item', 'color', ['required']) }">
                            <p v-if="v$.item.color.required.$invalid">
                                Color is required.
                            </p>
                        </div>
                        <div v-if="errors?.color" class="text-danger">
                            {{ errors?.color[0] }}
                        </div>
                    </div>
                    <div class="col-4">
                        <label>Order <span class="text-danger" v-if="edit">*</span></label>
                        <Input type="text" :disabled="!edit" v-model="item.order" :class="{ inputInvalidClass: checkInputValidity('item', 'order', ['required']) }" required autocomplete="item_order" />
                        <div v-if="v$.item.order.$dirty"
                            :class="{ 'text-danger': checkInputValidity('item', 'order', ['required']) }">
                            <p v-if="v$.item.order.required.$invalid">
                                Order is required.
                            </p>
                        </div>
                        <div v-if="errors?.order" class="text-danger">
                            {{ errors?.order[0] }}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label>Description</label>
                        <textarea class="form-control" :disabled="!edit" rows="5" v-model="item.description" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Status</label>
                        <div class="form-check form-switch d-flex align-items-center" style="margin:0">
                            <input class="form-check-input" type="checkbox" :disabled="!edit" value="" id="example-switch-default1" name="example-switch-default1" :checked="item.active" @change="handleStatusToggle"/>
                            <label class="form-check-label" for="example-switch-default1">
                                <span :class="`btn btn-sm ms-2 px-4 btn-${ item.active ? 'success': 'danger'} btn-xs rounded-pill`">{{ item.active ? "Active" : "Inactive" }}</span>
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
import defaultItem from '@/../../public/storage/default_images/shop.png';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { formatDate, formatDateSlash } from '@/helpers/Formatter/Date.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'Item Details',
        props:{
            item_details: [Object, Array],
            item_id: [Number],
            index: [Number]
        },
        emits: ['updateItem'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        data(){
            return{
                defaultItemImage: defaultItem,
                events:[],
                loadingEvents:false,
                image:null,
                delete_image:false,
                file:null,
                errors:[{
                    name:false,
                    quantity:false,
                    chance_rate:false,
                    order:false,
                    color:false,
                    event:false,
                }],
                item:{
                    name:null,
                    description:null,
                    quantity:null,
                    price:null,
                    color:null,
                    event:null,
                },
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                prevDetails:null,
                shops:[],
                loadingShops:false,
            }
        },
       
        validations () {
            return {
                item: {
                    name: { required },
                    quantity: { required },
                    chance_rate: { required },
                    order: { required },
                    color: { required },
                    event: { required },
                },
            }
        },
        components:{
            Modal,
            Input,
            VueDatePicker,
            VueMultiselect
        },

        async created(){
            this.getEvents();
        },

        methods:{
            async getEvents(){
                this.loadingEvents = true;
                await axios.get('/api/raffle/get-events', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { events } = response.data;
                    this.events = events;
                    this.loadingEvents = false;
                })
                .catch((error) =>{
                    console.log(error,'error');
                });
            },


            resetForm() {
                this.image   = null;
                this.file    = null;
                const form = document.querySelector('#shop-details-form');
                if(form){
                    form.reset();
                }
                this.delete_image = false;
                this.item = deepClone(this.prevDetails);
                this.image   = deepClone(this.prevDetails.item_image);
                this.errors = []; 
                this.v$.$reset();
            },

            editDetails(){
                this.edit = true;
            },

            cancelEdit(){
                this.edit = false;
                this.resetForm();
            },

            uploadImage(e){
                e.preventDefault()
                const file = e.target.files[0];
                this.file = file;
                if (file) {
                    // Use FileReader to read the file as a data URL
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.image = reader.result; // Set the imageUrl to the data URL
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeImage(){
                this.file = null;
                this.image = this.defaultItemImage;
                if(this.item.image){
                    this.delete_image = true;
                }
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            formatData(data){
                const details = {                            
                        ...data,
                        status:data.active ? 'Active' : 'Inactive',
                        created_at: formatDateSlash(data.created_at),
                        updated_at: formatDateSlash(data.updated_at),
                        event_name:data.event.name,
                        event:{label:data.event.name, value:data.event.id},
                }
                return details;
            },

            closeModal(){
                const id = document.getElementById('item-details-modal');
                const modal = bootstrap.Modal.getOrCreateInstance(id);
                this.resetForm();
                modal.hide();
            },

            async update(){
                this.isUpdating = true;

                const formData = new FormData();

                if (this.file) {
                    formData.append('item_image', this.file);
                }

                formData.append('id', this.item_id);
                formData.append('name', this.item.name);
                formData.append('description', this.item.description ?? '');
                formData.append('order', this.item.order);
                formData.append('quantity', this.item.quantity);
                formData.append('chance_rate', this.item.chance_rate);
                formData.append('color', this.item.color);
                formData.append('delete_image', this.delete_image);
                formData.append('active', this.item.active ? 1 : 0);
                formData.append('event_id', this.item.event.value);

                axios.post('/api/raffle/update-item',formData,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { data } = response.data;

                    this.isUpdating = false;
                    if(response.data?.errors) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                        return;
                    }else{
                        this.edit = false;
    
                        const formattedData = this.formatData(data);
    
                        this.prevDetails = deepClone(formattedData);
                        
                        this.$emit('updateItem', this.index, formattedData);
    
                        SwalDefault.fire({
                            icon: "success",
                            text: "Succesfully updated!",
                            showConfirmButton: false,
                            timer: 1500
                        });
    
                        this.resetForm();
                        this.errors = []; 
                    }

                }).catch((error) => {
                    this.isUpdating = false;
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                        SwalDefault.close();
                    }
                });
            },


            async updateConfirmation(){
                if(!await this.v$.$validate()){
                    return;
                }
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.update()
                    }
                });
            },

            handleStatusToggle() {
                this.item.active = !this.item.active;
            },
        },

        mounted(){
        },

        watch:{
            item_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.item_details);
                this.resetForm();
                this.item = this.item_details;
                this.image  = this.item_details.item_image; 
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