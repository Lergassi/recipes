export interface RecipeApiInterface {
    id: number;
    name: string;
    head_commit_id: number;
    products: {
        reference_product: {
            id: number;
            name: string;
        },
        weight: number,
    }[];
}