export interface RecipeInterface {
    id: number;
    name: string;
    products: {
        reference_product: {
            id: number;
            name: string;
        },
        weight: number,
    }[];
}