import React from "react";

type PaginationInputProps = {
  paged: number;
  setPage: (page: number) => void;
};

export default function PaginationInput({ paged, setPage }: PaginationInputProps) {
  return (
    <input
      aria-describedby="table-paging"
      className="current-page"
      value={paged}
      name="paged"
      size="1"
      type="text"
      onChange={(e) => setPage(Number(e.target.value))}
    />
  );
}
