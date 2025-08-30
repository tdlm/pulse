import React from "react";
import PaginationButton from "./pagination-button";
import PaginationInput from "./pagination-input";

type PaginationProps = {
  paged: number;
  setPage: (page: number) => void;
  totalPages: number;
};
export default function Pagination({
  paged,
  setPage,
  totalPages,
}: PaginationProps) {
  return (
    <span className="pagination-links">
      <PaginationButton
        setPage={setPage}
        type="first"
        page={1}
        currentPage={Number(paged)}
        totalPages={totalPages}
      />
      <PaginationButton
        setPage={setPage}
        type="previous"
        page={Number(paged) - 1}
        currentPage={Number(paged)}
        totalPages={totalPages}
      />
      <span className="paging-input">
        <label htmlFor="current-page-selector" className="screen-reader-text">
          Current Page
        </label>
        <PaginationInput paged={Number(paged)} setPage={setPage} />
        <span className="tablenav-paging-text">
          {" "}
          of <span className="total-pages">{totalPages}</span>
        </span>
      </span>
      <PaginationButton
        setPage={setPage}
        type="next"
        page={Number(paged) + 1}
        currentPage={Number(paged)}
        totalPages={totalPages}
      />
      <PaginationButton
        setPage={setPage}
        type="last"
        page={totalPages}
        currentPage={Number(paged)}
        totalPages={totalPages}
      />
    </span>
  );
}
