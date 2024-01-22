type term = 'location' | 'title' | 'salary';

type type = 'asc' | 'desc';

interface Ordertypes{
    name: string,
    value: string,
}

export { term, type, Ordertypes };