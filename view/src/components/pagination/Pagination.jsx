import React from 'react';

const Pagination = ({ currentPage, totalPages, onPageChange }) => {
    const range = 2;
    const pages = [];

    for (let i = 1; i <= totalPages; i++) {
        if (i >= currentPage - range && i <= currentPage + range) {
            pages.push(i);
        }
    }

    const handlePrev = () => {
        if (currentPage > 1) {
            onPageChange(currentPage - 1);
        }
    };

    const handleNext = () => {
        if (currentPage < totalPages) {
            onPageChange(currentPage + 1);
        }
    };

    return (
        <div className="pagination">
            {currentPage > 1 && (
                <button className="pagination__button" onClick={handlePrev}>
                    &lt;
                </button>
            )}

            {pages.map(page => (
                <button
                    key={page}
                    className={`pagination__button ${page === currentPage ? 'active' : ''}`}
                    onClick={() => onPageChange(page)}
                >
                    {page}
                </button>
            ))}

            {currentPage < totalPages && (
                <button className="pagination__button" onClick={handleNext}>
                    &gt;
                </button>
            )}
        </div>
    );
};

export default Pagination;
