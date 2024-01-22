interface Order{
    quantity: number,
    size: number,
}

type Items<T> = T[];



export { Order, Items};