<template>
    <input :type="type" :value="modelValue" class="form-control" @input="$emit('update:modelValue', $event.target.value)"/>
</template>
<script>
export default {
  props: {
            modelValue:[String, Number],
            type:{
                type:String,
                default:'text'
            },
            classes: {
                type: Object,
                default: () => ({})
            }
        },
  emits: ['update:modelValue']
}
</script>

