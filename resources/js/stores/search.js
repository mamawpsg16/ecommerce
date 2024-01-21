import { defineStore } from 'pinia'

export const useProductStore = defineStore('product', {
  state: () => {
    return { 
        items: [],
        count: 0
    }
  },
  getters: {
    doubleCount: (state) => {
      state.count = state.count * 2;
      console.log(state.count,'this.count SHEESH');
    },
  },
  actions: {
    setResults(items) {
        this.items =  items;
    },
    increment(items) {
        this.count++;
        console.log(this.count,'this.count SHEESH');
    },
  },
})