import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
  state: () => {
    return { 
        count: 0
    }
  },
  getters: {
    
  },
  actions: {
    setCount(count) {
        this.count =  count;
    },
  },
})