import { useEffect, useRef, useState } from "react";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import image from "../../assets/img.jpeg"

gsap.registerPlugin(ScrollTrigger);

// Navbar Component
const Navbar = () => {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const navRef = useRef(null);

    const handleNavClick = (e, targetId) => {
        e.preventDefault();
        setIsMenuOpen(false);
        const element = document.getElementById(targetId);
        if (element) {
            element.scrollIntoView({ behavior: "smooth" });
        }
    };

    const navItems = [
        { label: "Features", target: "clover-features" },
        { label: "Transfers & History", target: "clover-services" },
        { label: "Security", target: "clover-security" },
        { label: "Get Started", target: "clover-cta" },
    ];

    return (
        <header
            ref={navRef}
            className="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-emerald-100/50 shadow-sm"
        >
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex items-center justify-between h-16 md:h-20">
                    {/* Logo */}
                    <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg">
                            <span className="text-white text-xl">🍀</span>
                        </div>
                        <span className="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">
              Clover Bank
            </span>
                    </div>

                    {/* Desktop Navigation */}
                    <nav className="hidden md:flex items-center space-x-8">
                        {navItems.map((item) => (
                            <a
                                key={item.target}
                                href={`#${item.target}`}
                                onClick={(e) => handleNavClick(e, item.target)}
                                className="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200 hover:scale-105 transform"
                            >
                                {item.label}
                            </a>
                        ))}
                        <a href='https://expo.dev/artifacts/eas/vRhrkcgVFbE9t4n94EfP9J.apk' className="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-6 py-2.5 rounded-full font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                            Download App
                        </a>
                    </nav>

                    {/* Mobile Menu Button */}
                    <button
                        onClick={() => setIsMenuOpen(!isMenuOpen)}
                        className="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {isMenuOpen ? (
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            ) : (
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                            )}
                        </svg>
                    </button>
                </div>

                {/* Mobile Menu */}
                {isMenuOpen && (
                    <div className="md:hidden border-t border-gray-100 py-4 animate-fadeIn">
                        <div className="flex flex-col space-y-4">
                            {navItems.map((item) => (
                                <a
                                    key={item.target}
                                    href={`#${item.target}`}
                                    onClick={(e) => handleNavClick(e, item.target)}
                                    className="text-gray-700 hover:text-emerald-600 font-medium py-2 px-4 rounded-lg hover:bg-emerald-50 transition-colors duration-200"
                                >
                                    {item.label}
                                </a>
                            ))}
                            <a href='https://expo.dev/artifacts/eas/vRhrkcgVFbE9t4n94EfP9J.apk' className="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-6 py-3 rounded-full font-semibold mt-4 shadow-lg">
                                Download App
                            </a>
                        </div>
                    </div>
                )}
            </div>
        </header>
    );
};

// Hero Section
const Hero = () => {
    const heroRef = useRef(null);
    const mockupRef = useRef(null);
    const blob1Ref = useRef(null);
    const blob2Ref = useRef(null);

    // Floating blob animation
    useEffect(() => {
        if (blob1Ref.current && blob2Ref.current) {
            gsap.to(blob1Ref.current, {
                y: 20,
                duration: 3,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });

            gsap.to(blob2Ref.current, {
                y: -20,
                duration: 3.5,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut",
                delay: 0.5
            });
        }
    }, []);

    const handleScrollToFeatures = (e) => {
        e.preventDefault();
        document.getElementById('clover-features').scrollIntoView({ behavior: 'smooth' });
    };

    return (
        <section id="clover-hero" ref={heroRef} className="relative min-h-screen pt-24 md:pt-32 overflow-hidden">
            {/* Background Blobs */}
            <div
                ref={blob1Ref}
                className="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-emerald-300/30 to-teal-400/20 rounded-full blur-3xl"
            />
            <div
                ref={blob2Ref}
                className="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-emerald-400/20 to-cyan-400/20 rounded-full blur-3xl"
            />

            <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div className="grid lg:grid-cols-2 gap-12 items-center">
                    {/* Left Content */}
                    <div className="animate-section">
                        <div className="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 font-medium mb-8">
                            <span className="mr-2">🍀</span> Your Money, Simplified
                        </div>

                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                            Banking that's{" "}
                            <span className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Secure
              </span>{" "}
                            and{" "}
                            <span className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Simple
              </span>
                        </h1>

                        <p className="text-lg md:text-xl text-gray-600 mb-8 leading-relaxed">
                            Experience next-generation digital banking with real-time balance viewing,
                            instant fund transfers, and comprehensive transaction monitoring—all protected
                            by enterprise-grade security.
                        </p>

                        <div className="flex flex-col sm:flex-row gap-4 mb-10">
                            <a href='https://expo.dev/artifacts/eas/vRhrkcgVFbE9t4n94EfP9J.apk' className="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-8 py-3.5 rounded-full font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 text-lg">
                                Download App
                            </a>
                            <button
                                onClick={handleScrollToFeatures}
                                className="border-2 border-emerald-500 text-emerald-600 px-8 py-3.5 rounded-full font-semibold hover:bg-emerald-50 hover:scale-105 transition-all duration-200 text-lg"
                            >
                                View Features
                            </button>
                        </div>

                        {/* Social Proof */}
                        <div className="flex items-center space-x-4">
                            <div className="flex -space-x-3">
                                {[1, 2, 3].map((i) => (
                                    <div
                                        key={i}
                                        className="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 border-2 border-white shadow"
                                    />
                                ))}
                            </div>
                            <div className="text-gray-700">
                                <span className="font-semibold">50+ happy users</span>
                                <span className="mx-2">·</span>
                                <span className="text-amber-500 font-semibold">5.0 ★ rating</span>
                            </div>
                        </div>
                    </div>

                    {/* Right Content - Mockup */}
                    <div className="animate-section flex justify-end" ref={mockupRef}>
                        <img src={image} className=' shadow-xl'/>
                    </div>
                </div>
            </div>
        </section>
    );
};

// Features Section
const Features = () => {
    const features = [
        {
            icon: "🔐",
            title: "Secure Account Access",
            description: "Secure user registration & login with multi-factor authentication and biometric verification."
        },
        {
            icon: "📊",
            title: "Real-Time Balance View",
            description: "Instant updates on your finances with detailed insights and spending categorization."
        },
        {
            icon: "⚡",
            title: "Instant Fund Transfers",
            description: "Send money instantly to any bank account with zero delays and complete transaction security."
        },
        {
            icon: "📈",
            title: "Complete Transaction History",
            description: "Monitor all transactions with detailed records, search filters, and export capabilities."
        },
        {
            icon: "👤",
            title: "Profile & Password Management",
            description: "Easy profile updates and secure password management with regular security reminders."
        },
        {
            icon: "🛡️",
            title: "Admin Dashboard",
            description: "Comprehensive admin tools for internal team management and system oversight."
        }
    ];

    return (
        <section id="clover-features" className="py-20 md:py-28 bg-gradient-to-b from-white to-emerald-50/50">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center max-w-3xl mx-auto mb-16">
                    <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                        Your Essential Banking,{" "}
                        <span className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Secured and Simplified
            </span>
                    </h2>
                    <p className="text-lg md:text-xl text-gray-600">
                        All the tools you need for modern, day-to-day financial management.
                    </p>
                </div>

                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {features.map((feature, index) => (
                        <div
                            key={index}
                            className="animate-section bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 border border-emerald-100"
                        >
                            <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center mb-6">
                                <span className="text-2xl">{feature.icon}</span>
                            </div>
                            <h3 className="text-xl font-bold mb-4 text-gray-800">{feature.title}</h3>
                            <p className="text-gray-600 leading-relaxed">{feature.description}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

// Services Section
const Services = () => {
    return (
        <section id="clover-services" className="py-20 md:py-28 bg-white">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid lg:grid-cols-2 gap-12 items-center">
                    {/* Left Side - Mockup */}
                    <div className="animate-section order-2 lg:order-1">
                        <div className="relative max-w-lg mx-auto">
                            <div className="absolute -inset-4 bg-gradient-to-r from-emerald-400/20 to-teal-400/20 rounded-3xl blur-xl" />
                            <div className="relative bg-gradient-to-br from-white to-emerald-50 rounded-2xl shadow-2xl p-8 border border-emerald-100">
                                {/* Transfer Form */}
                                <div className="mb-8">
                                    <h4 className="text-xl font-bold mb-6 text-gray-800">Quick Transfer</h4>
                                    <div className="space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2">Recipient</label>
                                            <input
                                                type="text"
                                                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"
                                                placeholder="Enter name or account number"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                                            <input
                                                type="text"
                                                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        <button className="w-full bg-gradient-to-r from-emerald-500 to-emerald-700 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg transition-shadow duration-200">
                                            Send Money Now
                                        </button>
                                    </div>
                                </div>

                                {/* Recent Activity */}
                                <div>
                                    <h4 className="text-xl font-bold mb-6 text-gray-800">Recent Activity</h4>
                                    <div className="space-y-3">
                                        {[
                                            { action: "Sent to Sarah", amount: "-250.00", status: "Completed" },
                                            { action: "Received from John", amount: "+500.00", status: "Completed" },
                                            { action: "Bill Payment", amount: "-89.99", status: "Processing" },
                                        ].map((item, index) => (
                                            <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                                                <div>
                                                    <div className="font-medium text-gray-800">{item.action}</div>
                                                    <div className={`text-sm font-medium ${item.status === 'Completed' ? 'text-emerald-600' : 'text-amber-600'}`}>
                                                        {item.status}
                                                    </div>
                                                </div>
                                                <div className={`font-bold ${item.amount.startsWith('+') ? 'text-emerald-600' : 'text-gray-800'}`}>
                                                    {item.amount}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Side - Content */}
                    <div className="animate-section order-1 lg:order-2">
                        <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                            Effortless Money Movement and{" "}
                            <span className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Real-Time Tracking
              </span>
                        </h2>
                        <p className="text-lg text-gray-600 mb-8 leading-relaxed">
                            Experience seamless financial management with instant fund transfers, real-time balance
                            overview, and crystal-clear transaction history—all in one secure platform.
                        </p>

                        <div className="space-y-6">
                            {[
                                {
                                    icon: "⚡",
                                    title: "Instant Transfers",
                                    description: "Send money anywhere, anytime with immediate processing."
                                },
                                {
                                    icon: "👁️",
                                    title: "Live Balance Updates",
                                    description: "See your balance change in real-time as transactions occur."
                                },
                                {
                                    icon: "📋",
                                    title: "Detailed History",
                                    description: "Complete transaction logs with searchable records and filters."
                                }
                            ].map((item, index) => (
                                <div key={index} className="flex items-start space-x-4">
                                    <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center flex-shrink-0">
                                        <span className="text-xl">{item.icon}</span>
                                    </div>
                                    <div>
                                        <h4 className="text-xl font-bold mb-2 text-gray-800">{item.title}</h4>
                                        <p className="text-gray-600">{item.description}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

// Security Section
const Security = () => {
    const securityFeatures = [
        {
            icon: "🔒",
            title: "Secure Authentication",
            description: "Multi-factor authentication with biometric verification ensures only you can access your account."
        },
        {
            icon: "🛡️",
            title: "Encrypted Transactions",
            description: "End-to-end encryption for all fund transfers and transaction processing."
        },
        {
            icon: "👁️",
            title: "Transaction Monitoring",
            description: "24/7 real-time monitoring of all transactions with instant fraud detection."
        },
        {
            icon: "🔑",
            title: "Password Policies",
            description: "Strong password requirements and regular security updates for maximum protection."
        }
    ];

    return (
        <section id="clover-security" className="py-20 md:py-28 bg-gradient-to-b from-white to-emerald-50/50">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center max-w-3xl mx-auto mb-16">
                    <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                        Protection from{" "}
                        <span className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Registration to Transfer
            </span>
                    </h2>
                    <p className="text-lg md:text-xl text-gray-600">
                        Enterprise-grade security measures ensure your financial data and transactions
                        are protected at every step.
                    </p>
                </div>

                <div className="grid md:grid-cols-2 gap-8">
                    {securityFeatures.map((feature, index) => (
                        <div
                            key={index}
                            className="animate-section bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 border border-emerald-100"
                        >
                            <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center mb-6">
                                <span className="text-2xl">{feature.icon}</span>
                            </div>
                            <h3 className="text-xl font-bold mb-4 text-gray-800">{feature.title}</h3>
                            <p className="text-gray-600 leading-relaxed">{feature.description}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

// CTA Section
const CTA = () => {
    return (
        <section id="clover-cta" className="py-20 md:py-28 relative overflow-hidden">
            <div className="absolute inset-0 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700" />
            {/*<div className="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.05" fill-rule="evenodd"%3E%3Ccircle cx="3" cy="3" r="3"/%3E%3Ccircle cx="13" cy="13" r="3"/%3E%3C/g%3E%3C/svg%3E')]" />*/}

            <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div className="max-w-3xl mx-auto text-center animate-section">
                    <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 text-white">
                        Ready for Simple and Secure Banking?
                    </h2>
                    <p className="text-lg md:text-xl text-emerald-100 mb-10">
                        Join over 50,000 satisfied users who trust Clover Bank with their financial needs.
                        Download now and experience banking reimagined.
                    </p>
                    <a href='https://expo.dev/artifacts/eas/vRhrkcgVFbE9t4n94EfP9J.apk' className="bg-white text-emerald-700 px-10 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-3xl hover:scale-105 transition-all duration-300">
                        Download App Now
                    </a>

                    <div className="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                        {[
                            { value: "99.9%", label: "Uptime" },
                            { value: "256-bit", label: "Encryption" },
                            { value: "Instant", label: "Transfers" },
                            { value: "24/7", label: "Support" }
                        ].map((stat, index) => (
                            <div key={index} className="text-white">
                                <div className="text-2xl md:text-3xl font-bold mb-2">{stat.value}</div>
                                <div className="text-emerald-100">{stat.label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
};

// Footer
const Footer = () => {
    return (
        <footer className="bg-neutral-900 text-white pt-12 pb-8">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid md:grid-cols-4 gap-8 mb-8">
                    {/* Brand */}
                    <div>
                        <div className="flex items-center space-x-3 mb-6">
                            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
                                <span className="text-white text-xl">🍀</span>
                            </div>
                            <span className="text-2xl font-bold text-white">Clover Bank</span>
                        </div>
                        <p className="text-gray-400 mb-4">
                            Secure registration, instant transfers, and clear balance viewing—all in one modern banking platform.
                        </p>
                    </div>

                    {/* Quick Links */}
                    <div>
                        <h4 className="text-lg font-bold mb-6">Account</h4>
                        <ul className="space-y-3 text-gray-400">
                            <li><a href="#" className="hover:text-white transition-colors">Dashboard</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Transfers</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">History</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Security</a></li>
                        </ul>
                    </div>

                    {/* Company */}
                    <div>
                        <h4 className="text-lg font-bold mb-6">Company</h4>
                        <ul className="space-y-3 text-gray-400">
                            <li><a href="#" className="hover:text-white transition-colors">About Us</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Careers</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Press</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Blog</a></li>
                        </ul>
                    </div>

                    {/* Legal */}
                    <div>
                        <h4 className="text-lg font-bold mb-6">Legal</h4>
                        <ul className="space-y-3 text-gray-400">
                            <li><a href="#" className="hover:text-white transition-colors">Privacy Policy</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Terms of Service</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Cookie Policy</a></li>
                            <li><a href="#" className="hover:text-white transition-colors">Compliance</a></li>
                        </ul>
                    </div>
                </div>

                <div className="border-t border-neutral-800 pt-8 text-center text-gray-400">
                    <p>© {new Date().getFullYear()} Clover Bank. All rights reserved.</p>
                    <p className="mt-2 text-sm">Banking services provided by FDIC-insured institutions.</p>
                </div>
            </div>
        </footer>
    );
};

// Main Home Component
const Home = () => {
    const heroRef = useRef(null);
    const navbarRef = useRef(null);

    useEffect(() => {
        // Hero entry animations
        const tl = gsap.timeline({ defaults: { duration: 0.8, ease: "power3.out" } });

        // Navbar animation
        tl.from(navbarRef.current, {
            y: -40,
            opacity: 0,
            duration: 0.6
        });

        // Hero text elements with stagger
        tl.from(heroRef.current?.querySelectorAll('.animate-section > *'), {
            y: 40,
            opacity: 0,
            stagger: 0.15,
            duration: 0.8
        }, "-=0.3");

        // Hero mockup specific animation
        const mockup = heroRef.current?.querySelector('.animate-section[ref]');
        if (mockup) {
            tl.from(mockup, {
                y: 60,
                opacity: 0,
                scale: 0.9,
                rotationX: 10,
                duration: 1
            }, "-=0.8");
        }

        // Section reveal animations
        const animateSections = gsap.utils.toArray(".animate-section");
        animateSections.forEach((section, index) => {
            if (index > 0) { // Skip hero sections already animated
                gsap.from(section, {
                    opacity: 0,
                    y: 60,
                    duration: 0.8,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: section,
                        start: "top 80%",
                        end: "bottom 20%",
                        toggleActions: "play none none reverse"
                    }
                });
            }
        });

        // Parallax effect for hero mockup on scroll
        const mockupElement = heroRef.current?.querySelector('.animate-section[ref]');
        if (mockupElement) {
            gsap.to(mockupElement, {
                y: -30,
                ease: "none",
                scrollTrigger: {
                    trigger: heroRef.current,
                    start: "top top",
                    end: "bottom top",
                    scrub: 0.5
                }
            });
        }

        return () => {
            ScrollTrigger.getAll().forEach(trigger => trigger.kill());
        };
    }, []);

    return (
        <div className="min-h-screen bg-white">
            <div ref={navbarRef}>
                <Navbar />
            </div>
            <main>
                <div ref={heroRef}>
                    <Hero />
                </div>
                <Features />
                <Services />
                <Security />
                <CTA />
            </main>
            <Footer />
        </div>
    );
};

export default Home;
