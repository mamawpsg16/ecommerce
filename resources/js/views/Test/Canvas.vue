<template>
    <canvas id="tutorial" width="500" height="500"></canvas>
  </template>
  
  <script>
    export default {  
      mounted(){
        this.draw()
      },
      methods:{
        draw() {
          const canvas = document.getElementById("tutorial");
          if (canvas.getContext) {
            /** 
             * COLOR ctx.fillStyle - SHOULD BE DECLARED BEFORE THE SHAPE 
             * DRAW RECTANGLE ctx.fillRect
             * DRAW RECTANGLE BORDER ctx.strokeRect
             * CLEAR RECTANGLE STYLE ctx.clearRect
            * */
  
            const ctx = canvas.getContext("2d");
            // ctx.fillStyle = "rgb(255,0,0)";
            // ctx.fillRect(25, 25, 100, 100);
            // ctx.clearRect(45, 45, 60, 60);
            // ctx.strokeRect(50, 50, 50, 50);
            // First path
            ctx.beginPath();
            ctx.moveTo(25, 50);
            ctx.lineTo(100, 50);
            ctx.lineTo(100, 100);
            ctx.fill();
          }
        }
      }
      
    }
  </script>
  
  <style lang="scss" scoped>
  canvas {
          border: 1px solid black;
        }
  </style>