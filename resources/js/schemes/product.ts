import { z } from 'zod';

export const productFormScheme = z.object({
  id: z.number().optional(),
  name: z.string().min(1, '商品名を入力してください'),
  price: z.coerce.number().int().min(1, '商品価格は1以上で入力してください'),
  description: z.string().optional(),
});

export type ProductFormInput = z.input<typeof productFormScheme>;
export type ProductFormOutput = z.output<typeof productFormScheme>;
