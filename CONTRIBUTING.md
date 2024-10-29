# Contribution Guidelines

Thank you for considering contributing to the Laravel Filter project! Your contributions are essential for improving this package. Please follow the guidelines below to ensure a smooth collaboration.

## Etiquette

To maintain a positive and collaborative environment, please keep the following in mind:

- **Be Respectful**: Treat everyone in the community with respect. Personal attacks, derogatory comments, and unconstructive criticism will not be tolerated.
- **Stay on Topic**: Keep discussions focused on the issue or feature at hand. Avoid derailing conversations or bringing up unrelated topics.
- **Communicate Clearly**: When discussing issues, be as clear and concise as possible. Providing context and relevant information helps others understand and respond effectively.
- **Acknowledge Contributions**: Give credit to others where it's due, and be appreciative of the time and effort they put into their contributions.

By following these guidelines, you contribute to a welcoming and professional environment for everyone involved.

## Viability

Before investing time into a new feature or change, please consider the following guidelines to ensure your contribution aligns with the project's vision:

- **Alignment with Project Goals**: Priority is given to changes that align with the project's goals and improve its core functionality. We may not accept features that significantly increase complexity or deviate from these goals.
- **Scope and Complexity**: If a proposed feature is very large or could require extensive future maintenance, it may be better suited for a separate package.
- **Long-Term Sustainability**: Consider whether the change is likely to require ongoing updates. Features that are challenging to maintain may be discouraged unless they add significant value.
- **Community Interest**: Features that benefit a wide range of users or address common needs are more likely to be accepted than those serving highly specialized cases.

If you're unsure about the viability of a feature, feel free to open an issue to discuss it with maintainers before submitting a PR.

## Requirements

Please ensure your contributions meet the following requirements:

- **PSR-12 Coding Standard**: Adhere to the PSR-12 coding standard. The easiest way to apply this convention is by using PHP CodeSniffer.
- **Add Tests**: Contributions without tests cannot be accepted. Ensure your changes are well-tested to maintain code quality and reliability.
- **Document Changes in Behavior**: Update the README.md or any other relevant documentation to reflect any changes in behavior or new features.
- **Follow the Release Cycle**: We aim to follow SemVer v2.0.0, so avoid introducing breaking changes to public APIs without a major version update.
- **One Pull Request per Feature**: Limit each pull request to a single feature or bug fix. For multiple changes, please submit separate pull requests.
- **Coherent Commit History**: Keep a clean commit history. Squash intermediate commits to make sure each commit in your pull request is meaningful.

By following these requirements, you help maintain the quality, consistency, and readability of the project, ensuring it remains manageable and welcoming for all contributors.

## Prerequisites

Make sure you have the following installed:

- PHP 8.0 or higher 
- Composer
- Git

To set up the project, clone your fork and install dependencies:

```bash
git clone https://github.com/YOUR-USERNAME/laravel-filter.git
cd laravel-filter
composer install
```

## How to Contribute

### Reporting Issues

If you encounter any bugs or issues, please open an issue in the [GitHub Issues](https://github.com/brann-meius/laravel-filter/issues) section. When reporting an issue, please include:

- A clear and descriptive title.
- A description of the problem, including steps to reproduce it.
- Any relevant screenshots or code snippets.
- Your environment details (e.g., PHP version, Laravel version).

### Making Changes

1. **Fork the Repository**: Click the "Fork" button in the top right corner of this repository to create your own copy.

2. **Clone Your Fork**:
   ```bash
   git clone https://github.com/YOUR-USERNAME/laravel-filter.git
   cd laravel-filter
   ```

3. **Set Up the Development Environment**: Install dependencies:
   ```bash
   composer install
   ```

4. **Create a New Branch**: Use descriptive names for your branches:
   ```bash
   git checkout -b feature/your-feature-name
   ```

5. **Make Your Changes**:
   - Ensure that your code adheres to the coding standards outlined in the repository.
   - If you are adding new features, please write tests to cover your changes.

6. **Commit Your Changes**: Write clear and concise commit messages:
   ```bash
   git commit -m "feature/your-feature-name: Describe what you did."
   ```

7. **Push Your Changes**:
   ```bash
   git push origin feature/your-feature-name
   ```

8. **Create a Pull Request**: Navigate to the original repository and click on the "New Pull Request" button. Provide a clear description of your changes and why they should be merged.

## Coding Standards

Please ensure your code follows the project's coding standards. We use PHP CodeSniffer with the PSR-12 standard. You can run the following command to check for code style issues:

```bash
composer cs
```

## Testing

Before submitting your pull request, ensure all tests are passing. Run the tests using:

```bash
composer test
```

## Additional Tips

- Keep your pull requests focused on a single topic or feature to make reviewing easier.
- Run `composer cs` and `composer test` before each commit to ensure code quality and avoid back-and-forth changes.
- Use clear and descriptive names for branch names and commit messages for easy tracking.

Thank you for your interest in contributing to the Laravel Filter project!
