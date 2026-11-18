-- ============================================================================
-- OmniFAQ AI - MySQL Database Schema & Seed Data
-- Tech Interview Preparation Platform
-- Target Database: ai_faq_db
-- Engine: InnoDB
-- Character Set: utf8mb4
-- ============================================================================


-- ----------------------------------------------------------------------------
-- Table: users
-- Description: Stores registered user accounts and credentials.
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `search_analytics`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: faqs (Tech Interview Questions)
-- Description: Stores interview questions across tech domains & difficulty.
-- ----------------------------------------------------------------------------
CREATE TABLE `faqs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `difficulty` ENUM('Easy', 'Medium', 'Hard') NOT NULL DEFAULT 'Easy',
    `tags` VARCHAR(255) DEFAULT NULL,
    `views` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category`),
    INDEX `idx_difficulty` (`difficulty`),
    FULLTEXT INDEX `idx_ft_question_answer` (`question`, `answer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: search_analytics
-- Description: Tracks search queries and unresolved intent.
-- ----------------------------------------------------------------------------
CREATE TABLE `search_analytics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `query_text` VARCHAR(255) NOT NULL,
    `field_category` VARCHAR(50) DEFAULT 'General',
    `difficulty` VARCHAR(20) DEFAULT 'Easy',
    `matched_results_count` INT NOT NULL DEFAULT 0,
    `is_unresolved` BOOLEAN NOT NULL DEFAULT 0,
    `searched_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_searched_at` (`searched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Seed Data: Default Admin & User
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`full_name`, `email`, `password`, `role`) VALUES
('Admin User', 'admin@omnifaq.ai', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe10t5TdQWnNn/W4m7E.jA7k.KzB2b52', 'admin'),
('John Doe', 'john@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe10t5TdQWnNn/W4m7E.jA7k.KzB2b52', 'user');

-- ----------------------------------------------------------------------------
-- Seed Data: Tech Interview Questions
-- ----------------------------------------------------------------------------
INSERT INTO `faqs` (`question`, `answer`, `category`, `difficulty`, `tags`, `views`) VALUES

-- JAVASCRIPT --
(
    'What are closures in JavaScript and why are they useful?',
    'A closure is a function that retains access to its outer (enclosing) function''s variables even after the outer function has returned. Closures are created every time a function is created. They are useful for data privacy, creating factory functions, and maintaining state in asynchronous operations. Example: function outer() { let count = 0; return function inner() { return ++count; }; } — here inner() is a closure that remembers count.',
    'JavaScript',
    'Easy',
    'javascript,closures,scope,functions',
    142
),
(
    'Explain the difference between var, let, and const in JavaScript.',
    'var is function-scoped, hoisted to the top of its function, and can be redeclared. let is block-scoped, hoisted but not initialized (temporal dead zone), and cannot be redeclared in the same scope. const is also block-scoped like let, but its value cannot be reassigned after initialization (though object properties can still be mutated). Best practice: use const by default, let when reassignment is needed, avoid var.',
    'JavaScript',
    'Easy',
    'javascript,variables,scope,es6',
    198
),
(
    'What is the event loop in JavaScript and how does it work?',
    'The event loop is the mechanism that allows JavaScript to perform non-blocking I/O operations despite being single-threaded. It works by: 1) Executing synchronous code on the call stack, 2) Sending async operations (setTimeout, fetch, etc.) to Web APIs, 3) Completed callbacks are queued in the Task Queue (macrotasks) or Microtask Queue (Promises), 4) The event loop checks if the call stack is empty, then processes all microtasks first, then one macrotask, and repeats. Understanding this is crucial for predicting execution order.',
    'JavaScript',
    'Medium',
    'javascript,event-loop,async,concurrency',
    275
),
(
    'What is prototypal inheritance in JavaScript?',
    'In JavaScript, objects can inherit properties and methods from other objects through the prototype chain. Every object has an internal [[Prototype]] link. When you access a property on an object, JavaScript first looks on the object itself, then traverses up the prototype chain until it finds the property or reaches null. You can set prototypes using Object.create(), constructor functions with .prototype, or ES6 classes (which are syntactic sugar over prototypal inheritance). Key difference from classical inheritance: objects inherit directly from other objects, not from classes.',
    'JavaScript',
    'Hard',
    'javascript,prototype,inheritance,oop',
    185
),

-- REACT --
(
    'What is the Virtual DOM in React and why does it matter?',
    'The Virtual DOM is a lightweight, in-memory representation of the actual DOM. When state changes in a React component, React creates a new Virtual DOM tree and compares it with the previous one using a diffing algorithm (reconciliation). Only the differences (patches) are applied to the real DOM, minimizing expensive DOM operations. This makes React performant because: 1) Batch updates reduce reflows/repaints, 2) The diffing algorithm is O(n) using heuristics, 3) React can optimize updates across components.',
    'React',
    'Easy',
    'react,virtual-dom,performance,reconciliation',
    320
),
(
    'Explain the difference between useState and useReducer hooks.',
    'useState is ideal for simple, independent state values (toggles, counters, form inputs). useReducer is better for complex state logic involving multiple sub-values or when the next state depends on the previous one. useReducer accepts a reducer function (state, action) => newState and returns [state, dispatch]. It''s similar to Redux pattern. Use useReducer when: state logic is complex, multiple values are related, you want predictable state transitions, or you need to pass dispatch down instead of callbacks.',
    'React',
    'Medium',
    'react,hooks,state-management,useReducer',
    210
),
(
    'How does React''s reconciliation algorithm work?',
    'React''s reconciliation (diffing) algorithm compares two Virtual DOM trees using these heuristics: 1) Elements of different types produce different trees — React tears down the old tree and builds a new one. 2) Elements of the same type — React keeps the same DOM node and only updates changed attributes. 3) For child lists, React uses the "key" prop to match children between renders. Without keys, React re-renders the entire list. With keys, it can identify which items moved, were added, or removed. The algorithm runs in O(n) time complexity instead of O(n³) for a generic tree diff.',
    'React',
    'Hard',
    'react,reconciliation,diffing,performance,keys',
    165
),
(
    'What are React Server Components and how do they differ from Client Components?',
    'React Server Components (RSC) run only on the server and send rendered HTML to the client — they never ship JavaScript to the browser. Benefits: zero bundle size impact, direct database/filesystem access, automatic code splitting. Client Components (marked with "use client") run in the browser and handle interactivity (state, effects, event handlers). Key differences: RSCs cannot use hooks like useState/useEffect, cannot attach event handlers, but can import Client Components. Client Components cannot import Server Components directly but can receive them as children props.',
    'React',
    'Hard',
    'react,server-components,rsc,nextjs,rendering',
    95
),

-- NODE.JS --
(
    'What is middleware in Express.js and how does it work?',
    'Middleware functions are functions that have access to the request (req), response (res), and the next middleware function (next) in the application''s request-response cycle. They can: execute code, modify req/res objects, end the request-response cycle, or call next() to pass control to the next middleware. Types: Application-level (app.use), Router-level (router.use), Error-handling (4 args: err, req, res, next), Built-in (express.json(), express.static()), Third-party (cors, helmet, morgan). Middleware executes in the order it''s defined.',
    'Node.js',
    'Easy',
    'nodejs,express,middleware,backend',
    188
),
(
    'Explain the difference between REST and GraphQL APIs.',
    'REST uses multiple endpoints (GET /users, POST /users/:id) with fixed data structures. GraphQL uses a single endpoint where clients specify exactly what data they need via queries. Key differences: 1) Over/Under-fetching: REST often returns too much or too little data; GraphQL returns exactly what''s requested. 2) Versioning: REST uses URL versioning (v1/v2); GraphQL evolves schemas without versions. 3) Caching: REST leverages HTTP caching easily; GraphQL requires custom caching (Apollo Cache). 4) Learning curve: REST is simpler; GraphQL requires schema definition and resolver functions. Choose REST for simple CRUD APIs; GraphQL for complex, nested data requirements.',
    'Node.js',
    'Medium',
    'nodejs,rest,graphql,api,backend',
    245
),
(
    'How does Node.js handle concurrency despite being single-threaded?',
    'Node.js uses an event-driven, non-blocking I/O model powered by libuv. While JavaScript runs on a single thread (the main event loop), I/O operations (file reads, network requests, database queries) are delegated to the libuv thread pool (default 4 threads) or OS-level async mechanisms (epoll/kqueue/IOCP). When an I/O operation completes, its callback is queued and executed on the main thread. This architecture handles thousands of concurrent connections efficiently because the main thread never blocks waiting for I/O. For CPU-intensive tasks, use Worker Threads or child processes to avoid blocking the event loop.',
    'Node.js',
    'Hard',
    'nodejs,concurrency,event-loop,libuv,threads',
    178
),

-- SQL / DATABASE --
(
    'What is the difference between INNER JOIN and LEFT JOIN?',
    'INNER JOIN returns only rows that have matching values in both tables — if there''s no match, the row is excluded. LEFT JOIN (LEFT OUTER JOIN) returns ALL rows from the left table and the matched rows from the right table. If there''s no match, NULL values are returned for right table columns. Example: SELECT users.name, orders.total FROM users INNER JOIN orders ON users.id = orders.user_id — returns only users who have orders. Using LEFT JOIN would return all users, with NULL for those without orders. Use INNER JOIN when you need only matching data; LEFT JOIN when you need all records from one table regardless of matches.',
    'SQL',
    'Easy',
    'sql,joins,database,queries',
    290
),
(
    'Explain ACID properties in database transactions.',
    'ACID ensures reliable database transactions: Atomicity — a transaction is all-or-nothing; if any part fails, the entire transaction is rolled back. Consistency — a transaction brings the database from one valid state to another, enforcing all rules/constraints. Isolation — concurrent transactions don''t interfere with each other; each appears to execute serially (isolation levels: Read Uncommitted, Read Committed, Repeatable Read, Serializable). Durability — once a transaction is committed, it persists even during system failures (achieved via write-ahead logging). Example: A bank transfer must debit one account AND credit another atomically.',
    'SQL',
    'Medium',
    'sql,acid,transactions,database,consistency',
    215
),
(
    'What are Clustered vs Non-Clustered indexes and when to use each?',
    'A Clustered Index physically sorts and stores table data rows based on the index key — there can be only ONE per table (usually the primary key). A Non-Clustered Index creates a separate structure with pointers back to the actual data rows — you can have MULTIPLE per table. Clustered: faster for range queries (WHERE price BETWEEN 10 AND 50) since data is physically ordered. Non-Clustered: faster for specific lookups and useful for columns frequently used in WHERE, JOIN, ORDER BY clauses. Trade-offs: too many non-clustered indexes slow down INSERT/UPDATE/DELETE operations. B-Tree is the most common underlying structure for both.',
    'SQL',
    'Hard',
    'sql,indexes,performance,database,optimization',
    155
),
(
    'What is database normalization and what are the normal forms?',
    'Normalization is the process of organizing database tables to reduce data redundancy and improve data integrity. Normal forms: 1NF — eliminate repeating groups, ensure atomic values (each cell holds one value). 2NF — meet 1NF + remove partial dependencies (every non-key column depends on the entire primary key). 3NF — meet 2NF + remove transitive dependencies (non-key columns don''t depend on other non-key columns). BCNF — every determinant is a candidate key. Denormalization is sometimes used intentionally for read-heavy workloads to avoid expensive JOINs. Balance between normalization (write efficiency, integrity) and denormalization (read performance).',
    'SQL',
    'Medium',
    'sql,normalization,database,schema-design',
    170
),

-- SYSTEM DESIGN --
(
    'How would you design a URL shortener like bit.ly?',
    'Key components: 1) API Layer — POST /shorten accepts long URL, returns short code; GET /:code redirects to original URL. 2) ID Generation — use a counter + Base62 encoding (a-z, A-Z, 0-9) to generate short codes, or use MD5/SHA256 hash truncated to 7 chars. 3) Storage — key-value store (Redis) for fast lookups with a relational DB (MySQL/PostgreSQL) for persistence. Schema: id, short_code (indexed), long_url, created_at, expiry, click_count. 4) Redirection — 301 (permanent, cached) or 302 (temporary, tracked) redirect. 5) Scale — read-heavy system, use caching (Redis), database sharding by short_code hash, CDN for global distribution. Handle ~100:1 read-to-write ratio.',
    'System Design',
    'Medium',
    'system-design,url-shortener,scalability,architecture',
    310
),
(
    'Explain rate limiting strategies and how to implement them.',
    'Rate limiting controls the number of requests a client can make in a time window. Algorithms: 1) Fixed Window — count requests in fixed intervals (e.g., 100 req/min); simple but bursty at window edges. 2) Sliding Window Log — store timestamps of each request, count within rolling window; accurate but memory-intensive. 3) Sliding Window Counter — hybrid of fixed + sliding; balances accuracy and memory. 4) Token Bucket — bucket holds tokens refilled at fixed rate; requests consume tokens; allows controlled bursts. 5) Leaky Bucket — requests enter a queue processed at fixed rate; smooths traffic but can delay requests. Implementation: Use Redis with INCR + EXPIRE for distributed rate limiting. Return HTTP 429 Too Many Requests with Retry-After header.',
    'System Design',
    'Hard',
    'system-design,rate-limiting,api,security,scalability',
    195
),
(
    'What is load balancing and what are common algorithms?',
    'A load balancer distributes incoming network traffic across multiple servers to ensure no single server is overwhelmed. Algorithms: 1) Round Robin — requests distributed sequentially; simple, works well for equal-capacity servers. 2) Weighted Round Robin — assigns weights based on server capacity. 3) Least Connections — routes to server with fewest active connections; good for varied request durations. 4) IP Hash — routes based on client IP hash; ensures session persistence. 5) Least Response Time — combines least connections + fastest response. Types: Layer 4 (transport — TCP/UDP, faster) vs Layer 7 (application — HTTP, smarter routing based on content). Tools: Nginx, HAProxy, AWS ALB/NLB. Health checks ensure traffic only goes to healthy servers.',
    'System Design',
    'Medium',
    'system-design,load-balancing,scalability,infrastructure',
    230
),
(
    'How does caching work in distributed systems?',
    'Caching stores frequently accessed data in fast storage (RAM) to reduce latency and database load. Strategies: 1) Cache-Aside (Lazy Loading) — app checks cache first; on miss, reads from DB and populates cache. 2) Write-Through — writes go to cache AND DB simultaneously; ensures consistency but slower writes. 3) Write-Behind (Write-Back) — writes go to cache first, asynchronously flushed to DB; fast writes but risk of data loss. 4) Read-Through — cache sits between app and DB, handles fetching transparently. Eviction policies: LRU (Least Recently Used), LFU (Least Frequently Used), TTL (Time-To-Live). Challenges: cache invalidation, thundering herd, cache penetration (non-existent keys), cache avalanche (mass expiry). Tools: Redis, Memcached. Use CDNs for static content caching.',
    'System Design',
    'Hard',
    'system-design,caching,redis,distributed-systems,performance',
    175
);
