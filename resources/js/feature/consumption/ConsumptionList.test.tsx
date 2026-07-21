import { render, screen, within } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { router } from "@inertiajs/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import ConsumptionList from "./ConsumptionList";
import type { ProductInventory } from "@/types";

vi.mock("@inertiajs/react", () => ({
  router: {
    post: vi.fn(),
  },
}));

vi.mock("@/routes/shop/products", () => ({
  consumption: {
    url: ({ product }: { product: number }) => `/shop/products/${product}`,
  },
}));

const products: ProductInventory[] = [
  {
    id: 1,
    product_id: 1,
    name: "商品A",
    description: "商品Aの説明",
    price: 1000,
    status: { id: 2, label: "承認済み" },
    quantity: 10,
    created_at: "2026/07/21",
    updated_at: "2026/07/21",
  },
  {
    id: 2,
    product_id: 2,
    name: "商品B",
    description: null,
    price: 2000,
    status: { id: 2, label: "承認済み" },
    quantity: 0,
    created_at: "2026/07/21",
    updated_at: "2026/07/21",
  },
];

describe("ConsumptionList", () => {
  beforeEach(() => {
    vi.mocked(router.post).mockClear();
    vi.spyOn(window, "alert").mockImplementation(() => {});
  });

  it("商品一覧を表示する", () => {
    render(<ConsumptionList products={products} />);

    expect(screen.getByText("商品A")).toBeInTheDocument();
    expect(screen.getByText("商品Aの説明")).toBeInTheDocument();
    expect(screen.getByText("10")).toBeInTheDocument();
  });

  it("descriptionがnullの場合はフォールバック文言を表示する", () => {
    render(<ConsumptionList products={products} />);

    expect(screen.getByText("説明がありません")).toBeInTheDocument();
  });

  it("購入ボタンを押すとダイアログが開く", async () => {
    const user = userEvent.setup();
    render(<ConsumptionList products={products} />);

    const card = screen.getByText("商品A").closest(".MuiCard-root") as HTMLElement;
    await user.click(within(card).getByRole("button", { name: "購入" }));

    expect(screen.getByRole("dialog")).toBeInTheDocument();
    // expect(screen.getByText("商品販売")).toBeInTheDocument();
  });

  it("数量0のまま販売を押すとalertが表示され、router.postは呼ばれない", async () => {
    const user = userEvent.setup();
    render(<ConsumptionList products={products} />);

    const card = screen.getByText("商品A").closest(".MuiCard-root") as HTMLElement;
    await user.click(within(card).getByRole("button", { name: "購入" }));
    await user.click(screen.getByRole("button", { name: "販売" }));

    expect(window.alert).toHaveBeenCalledWith("数量を入力してください");
    expect(router.post).not.toHaveBeenCalled();
  });

  it("数量を入力して販売を押すとrouter.postが正しい引数で呼ばれる", async () => {
    const user = userEvent.setup();
    render(<ConsumptionList products={products} />);

    const card = screen.getByText("商品A").closest(".MuiCard-root") as HTMLElement;
    await user.click(within(card).getByRole("button", { name: "購入" }));

    const input = screen.getByLabelText("数量");
    await user.clear(input);
    await user.type(input, "3");

    await user.click(screen.getByRole("button", { name: "販売" }));

    expect(router.post).toHaveBeenCalledWith(
      "/shop/products/1",
      { count: 3 },
      expect.objectContaining({ onSuccess: expect.any(Function) })
    );
  });

  it("router.postのonSuccess実行後にダイアログが閉じ、完了alertが表示される", async () => {
    const user = userEvent.setup();
    vi.mocked(router.post).mockImplementation((_url, _data, options) => {
      options?.onSuccess?.({} as never);
      return {} as never;
    });

    render(<ConsumptionList products={products} />);

    const card = screen.getByText("商品A").closest(".MuiCard-root") as HTMLElement;
    await user.click(within(card).getByRole("button", { name: "購入" }));

    const input = screen.getByLabelText("数量");
    await user.clear(input);
    await user.type(input, "2");
    await user.click(screen.getByRole("button", { name: "販売" }));

    expect(window.alert).toHaveBeenCalledWith("追加購入が完了しました");
    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });
});