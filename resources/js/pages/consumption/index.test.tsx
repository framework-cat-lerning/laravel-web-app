import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import ConsumptionList from "@/feature/consumption/ConsumptionList";
import ConsumptionIndex from "./index";


vi.mock("@inertiajs/react", () => ({
  Head: ({ title }: { title: string }) => <title>{title}</title>,
}));

vi.mock("@/components/ui/Header", () => ({
  default: ({ title }: { title: string }) => <h1>{title}</h1>,
}));

vi.mock("@/feature/consumption/ConsumptionList", () => ({
  default: vi.fn(() => <div data-testid="consumption-list" />),
}));

describe("ConsumptionIndex", () => {
  it("タイトルとヘッダーを表示する", () => {
    render(
      <ConsumptionIndex
        products={{
          data: [],
        }}
      />
    );

    expect(screen.getByRole("heading", { name: "商品販売" })).toBeInTheDocument();
  });

  it("productsのdataをConsumptionListへ渡す", () => {
    const data = [
      {
        id: 1,
        product_id: 1,
        name: "商品A",
        description: "説明",
        price: 1000,
        status: { id: 2, label: "承認済み" },
        quantity: 5,
        created_at: "2026/07/21",
        updated_at: "2026/07/21",
      },
    ];
  
    render(<ConsumptionIndex products={{ data }} />);
  
    expect(ConsumptionList).toHaveBeenCalledWith(
      expect.objectContaining({ products: data }),
      undefined
    );
  });
});