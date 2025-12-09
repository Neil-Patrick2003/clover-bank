import React, { useState, useEffect } from "react";

const CloverNavbar: React.FC = () => {
    const [mobileOpen, setMobileOpen] = useState(false);

    const handleScrollTo = (e: React.MouseEvent<HTMLAnchorElement>, id: string) => {
        e.preventDefault();

        const target = document.getElementById(id);
        if (!target) return;

        const nav = document.getElementById("clover-nav");
        const navHeight = nav ? nav.offsetHeight : 0;

        const targetPosition = target.getBoundingClientRect().top + window.scrollY;
        const offsetPosition = targetPosition - navHeight;

        window.scrollTo({
            top: offsetPosition,
            behavior: "smooth",
        });

        setMobileOpen(false);
    };

    // Scroll animations for .animate-on-scroll (optional but matches your original)
    useEffect(() => {
        const elements = document.querySelectorAll(".animate-on-scroll");

        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px",
            }
        );

        elements.forEach(el => observer.observe(el));

        return () => {
            elements.forEach(el => observer.unobserve(el));
            observer.disconnect();
        };
    }, []);

    return (
        <nav
            id="clover-nav"
            className="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-emerald-100"
        >
            <div className="max-w-7xl mx-auto px-6 lg:px-8">
                <div className="flex items-center justify-between h-20">
                    {/* Brand */}
                    <div className="flex items-center space-x-2">
                        <div className="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center">
                            <img src="/cloverbank.png" alt="logo" />
                        </div>
                        <span className="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">
                            Clover Bank
                        </span>
                    </div>

                    {/* Desktop menu */}
                    <div className="hidden md:flex items-center space-x-8">
                        <a
                            href="#clover-features"
                            onClick={e => handleScrollTo(e, "clover-features")}
                            className="text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Features
                        </a>
                        <a
                            href="#clover-services"
                            onClick={e => handleScrollTo(e, "clover-services")}
                            className="text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Transfers &amp; History
                        </a>
                        <a
                            href="#clover-security"
                            onClick={e => handleScrollTo(e, "clover-security")}
                            className="text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Security
                        </a>
                        <a
                            href="#clover-cta"
                            onClick={e => handleScrollTo(e, "clover-cta")}
                            className="text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Get Started
                        </a>
                        <a
                            href="https://expo.dev/artifacts/eas/vRhrkcgVFbE9t4n94EfP9J.apk"
                            className="border rounded-full px-4 py-2 hover:scale-105 bg-gradient-to-l from-emerald-600 to-emerald-400 text-white transition-transform"
                        >
                            Download App
                        </a>
                    </div>

                    {/* Mobile toggle */}
                    <button
                        id="mobile-menu-button"
                        className="md:hidden"
                        onClick={() => setMobileOpen(prev => !prev)}
                    >
                        {/* Hamburger */}
                        <svg
                            className={`w-6 h-6 text-neutral-800 ${mobileOpen ? "hidden" : "block"}`}
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        {/* Close */}
                        <svg
                            className={`w-6 h-6 text-neutral-800 ${mobileOpen ? "block" : "hidden"}`}
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                {/* Mobile Menu */}
                <div
                    id="mobile-menu"
                    className={`mobile-menu md:hidden bg-white border-t border-emerald-100 ${
                        mobileOpen ? "open" : ""
                    }`}
                >
                    <div className="py-4 space-y-4">
                        <a
                            href="#clover-features"
                            onClick={e => handleScrollTo(e, "clover-features")}
                            className="block px-4 py-2 text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Features
                        </a>
                        <a
                            href="#clover-services"
                            onClick={e => handleScrollTo(e, "clover-services")}
                            className="block px-4 py-2 text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Transfers &amp; History
                        </a>
                        <a
                            href="#clover-security"
                            onClick={e => handleScrollTo(e, "clover-security")}
                            className="block px-4 py-2 text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Security
                        </a>
                        <a
                            href="#clover-cta"
                            onClick={e => handleScrollTo(e, "clover-cta")}
                            className="block px-4 py-2 text-neutral-600 hover:text-emerald-600 transition-colors font-medium"
                        >
                            Get Started
                        </a>
                        <div className="px-4 pt-2">
                            <a
                                href="https://expo.dev/artifacts/eas/vRhrkcgVFbE9t4n94EfP9J.apk"
                                className="w-full px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-lg font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg shadow-emerald-500/30 flex justify-center"
                            >
                                Download App
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    );
};

export default CloverNavbar;
