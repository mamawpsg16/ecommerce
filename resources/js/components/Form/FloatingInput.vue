<template>

    <!-- <FloatingInput 
    type="text" 
    label="First Name" 
    :isInvalid="{
       'is-invalid': v$.student.first_name.$dirty && (v$.student.first_name.required.$invalid)
     }" 
     v-model="student.first_name"  
     placeholder="First Name"
     autocomplete="first_name"
     >
   </FloatingInput>
   <div  v-if="v$.student.first_name.$dirty" :class="{ 'invalid-feedback':(v$.student.first_name.required.$invalid )}">
        <p v-if="v$.student.first_name.required.$invalid">
            First Name is required.
        </p>
    </div> -->
    
    <div class="form-floating mb-3" :class="classes">
        <input :type="type" :value="modelValue" class="form-control" @input="$emit('update:modelValue', $event.target.value)" :class="isInvalid" v-bind="$attrs"/>
        <label for="floatingInput">{{ label }} <span class="text-danger">*</span></label>
    </div>
</template>
<script>
export default {
  props: {
    modelValue:String,
    type:{
        type:String,
        default:'text'
    },
    label:{
        type:String,
        required:true
    },
    classes: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['update:modelValue']
}
</script>

